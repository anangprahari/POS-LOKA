import React, { Component } from "react";
import { createRoot } from "react-dom";
import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";

class Cart extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            products: [],
            customers: [],
            barcode: "",
            search: "",
            customer_id: "",
            translations: {},
        };

        this.loadCart = this.loadCart.bind(this);
        this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this);
        this.handleScanBarcode = this.handleScanBarcode.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.handleEmptyCart = this.handleEmptyCart.bind(this);
        this.loadProducts = this.loadProducts.bind(this);
        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSeach = this.handleSeach.bind(this);
        this.setCustomerId = this.setCustomerId.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this);
        this.loadTranslations = this.loadTranslations.bind(this);
    }

    componentDidMount() {
        this.loadTranslations();
        this.loadCart();
        this.loadProducts();
        this.loadCustomers();
    }

    loadTranslations() {
        axios
            .get("/admin/locale/cart")
            .then((res) => this.setState({ translations: res.data }))
            .catch((error) =>
                console.error("Error loading translations:", error)
            );
    }

    loadCustomers() {
        axios
            .get(`/admin/customers`)
            .then((res) => this.setState({ customers: res.data }));
    }

    loadProducts(search = "") {
        const query = search ? `?search=${search}` : "";
        axios
            .get(`/admin/products${query}`)
            .then((res) => this.setState({ products: res.data.data }));
    }

    loadCart() {
        axios
            .get("/admin/cart")
            .then((res) => this.setState({ cart: res.data }));
    }

    handleOnChangeBarcode(e) {
        this.setState({ barcode: e.target.value });
    }

    handleScanBarcode(e) {
        e.preventDefault();
        const { barcode } = this.state;
        if (!barcode) return;
        axios
            .post("/admin/cart", { barcode })
            .then(() => {
                this.loadCart();
                this.setState({ barcode: "" });
            })
            .catch((err) =>
                Swal.fire("Error!", err.response.data.message, "error")
            );
    }

    handleChangeQty(product_id, qty) {
        const cart = this.state.cart.map((c) =>
            c.id === product_id
                ? { ...c, pivot: { ...c.pivot, quantity: qty } }
                : c
        );
        this.setState({ cart });
        if (!qty) return;
        axios
            .post("/admin/cart/change-qty", { product_id, quantity: qty })
            .catch((err) =>
                Swal.fire("Error!", err.response.data.message, "error")
            );
    }

    getTotal(cart) {
        return sum(cart.map((c) => c.pivot.quantity * c.price)).toFixed(2);
    }

    handleClickDelete(product_id) {
        axios
            .post("/admin/cart/delete", { product_id, _method: "DELETE" })
            .then(() =>
                this.setState((state) => ({
                    cart: state.cart.filter((c) => c.id !== product_id),
                }))
            );
    }

    handleEmptyCart() {
        axios
            .post("/admin/cart/empty", { _method: "DELETE" })
            .then(() => this.setState({ cart: [] }));
    }

    handleChangeSearch(e) {
        this.setState({ search: e.target.value });
    }

    handleSeach(e) {
        if (e.keyCode === 13) this.loadProducts(e.target.value);
    }

    addProductToCart(barcode) {
        const product = this.state.products.find((p) => p.barcode === barcode);
        if (!product) return;

        if (product.quantity <= 0) {
            Swal.fire(
                "Error!",
                this.state.translations["product_out_of_stock"] ||
                    "Product is out of stock.",
                "error"
            );
            return;
        }

        axios
            .post("/admin/cart", { barcode })
            .then(() => {
                this.loadCart();
            })
            .catch((err) => {
                Swal.fire(
                    "Error!",
                    err.response?.data?.message ||
                        "Failed to add product to cart.",
                    "error"
                );
            });
    }

    setCustomerId(e) {
        this.setState({ customer_id: e.target.value });
    }

    handleClickSubmit() {
        Swal.fire({
            title: this.state.translations["received_amount"],
            input: "text",
            inputValue: this.getTotal(this.state.cart),
            cancelButtonText: this.state.translations["cancel_pay"],
            showCancelButton: true,
            confirmButtonText: this.state.translations["confirm_pay"],
            showLoaderOnConfirm: true,
            preConfirm: (amount) =>
                axios
                    .post("/admin/orders", {
                        customer_id: this.state.customer_id,
                        amount,
                    })
                    .then((res) => {
                        this.loadCart();
                        return res.data;
                    })
                    .catch((err) =>
                        Swal.showValidationMessage(err.response.data.message)
                    ),
            allowOutsideClick: () => !Swal.isLoading(),
        });
    }

    handleCancel = () => {
        this.handleEmptyCart();
    };

    handleCheckout = () => {
        this.handleClickSubmit();
    };

    render() {
        const { cart, products, customers, barcode, translations, search } =
            this.state;

        return (
            <div className="container-fluid py-3">
                {/* Inline global styles */}
                <style>
                    {`
            .cursor-pointer { cursor: pointer; }
            .rounded-pill { border-radius: 50rem !important; }
            .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; }
            .text-muted { color: #6c757d !important; }
            .text-danger { color: #dc3545 !important; }
            .text-center { text-align: center !important; }
            .text-end { text-align: end !important; }
            .form-control-sm { height: calc(1.5em + .5rem + 2px); padding: .25rem .5rem; font-size: .875rem; }
          `}
                </style>

                <div className="row">
                    {/* Sidebar Cart */}
                    <div className="col-12 col-lg-4 mb-4">
                        <div className="card shadow-sm h-100">
                            <div className="card-body d-flex flex-column">
                                {/* Barcode input */}
                                <form
                                    className="mb-3 d-flex gap-2 align-items-center"
                                    onSubmit={this.handleScanBarcode}
                                >
                                    <input
                                        type="text"
                                        className="form-control rounded-pill shadow-sm px-4"
                                        placeholder={
                                            translations["scan_barcode"]
                                        }
                                        value={barcode}
                                        onChange={this.handleOnChangeBarcode}
                                    />
                                    <button
                                        className="btn btn-success rounded-pill shadow-sm px-4 d-flex align-items-center"
                                        type="submit"
                                    >
                                        <i
                                            className="fas fa-barcode"
                                            style={{ marginRight: "5px" }}
                                        ></i>
                                        Scan
                                    </button>
                                </form>

                                {/* Customer Select */}
                                <select
                                    className="form-select mb-3"
                                    onChange={this.setCustomerId}
                                    value={this.state.customer_id}
                                >
                                    <option value="">
                                        {translations["general_customer"]}
                                    </option>
                                    {customers.map((cus) => (
                                        <option key={cus.id} value={cus.id}>
                                            {cus.first_name} {cus.last_name}
                                        </option>
                                    ))}
                                </select>

                                {/* Cart Items */}
                                <div className="flex-grow-1 overflow-auto">
                                    {cart.length ? (
                                        <table className="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        {
                                                            translations[
                                                                "product_name"
                                                            ]
                                                        }
                                                    </th>
                                                    <th>
                                                        {
                                                            translations[
                                                                "quantity"
                                                            ]
                                                        }
                                                    </th>
                                                    <th className="text-end">
                                                        {translations["price"]}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {cart.map((c) => (
                                                    <tr key={c.id}>
                                                        <td>{c.name}</td>
                                                        <td className="d-flex align-items-center">
                                                            <input
                                                                type="number"
                                                                min="1"
                                                                className="form-control form-control-sm me-2"
                                                                style={{
                                                                    width: "60px",
                                                                }}
                                                                value={
                                                                    c.pivot
                                                                        .quantity
                                                                }
                                                                onChange={(e) =>
                                                                    this.handleChangeQty(
                                                                        c.id,
                                                                        e.target
                                                                            .value
                                                                    )
                                                                }
                                                            />
                                                            <button
                                                                className="btn btn-sm btn-outline-danger"
                                                                onClick={() =>
                                                                    this.handleClickDelete(
                                                                        c.id
                                                                    )
                                                                }
                                                            >
                                                                <i className="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                        <td className="text-end">
                                                            {
                                                                window.APP
                                                                    .currency_symbol
                                                            }{" "}
                                                            {(
                                                                c.price *
                                                                c.pivot.quantity
                                                            ).toFixed(2)}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    ) : (
                                        <div className="text-center text-muted py-5">
                                            {translations["no_items"]}
                                        </div>
                                    )}
                                </div>

                                {/* Total & Buttons */}
                                <div className="mt-3">
                                    <div className="d-flex justify-content-between mb-3">
                                        <strong>
                                            {translations["total"]}:
                                        </strong>
                                        <strong>
                                            {window.APP.currency_symbol}{" "}
                                            {this.getTotal(cart)}
                                        </strong>
                                    </div>
                                    <div className="d-flex justify-content-between gap-2 mt-3">
                                        <button
                                            type="button"
                                            className="btn btn-danger rounded-pill px-4 shadow-sm w-50"
                                            onClick={this.handleCancel}
                                        >
                                            <i
                                                className="fas fa-times me-2"
                                                style={{ marginRight: "8px" }}
                                            ></i>
                                            {translations["cancel"]}
                                        </button>
                                        <button
                                            type="button"
                                            className="btn btn-primary rounded-pill px-4 shadow-sm w-50"
                                            onClick={this.handleCheckout}
                                        >
                                            <i
                                                className="fas fa-shopping-cart me-2"
                                                style={{ marginRight: "8px" }}
                                            ></i>
                                            {translations["checkout"]}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Product List */}
                    <div className="col-12 col-lg-8">
                        <div className="mb-3 d-flex gap-2">
                            <input
                                type="text"
                                className="form-control rounded-pill shadow-sm px-4"
                                placeholder={`${translations["search_product"]}...`}
                                value={search}
                                onChange={this.handleChangeSearch}
                                onKeyDown={this.handleSeach}
                                style={{ flex: 1 }}
                            />
                            {search && (
                                <button
                                    className="btn btn-outline-danger rounded-pill shadow-sm px-3 d-flex align-items-center"
                                    onClick={() => {
                                        this.setState({ search: "" });
                                        this.loadProducts("");
                                    }}
                                >
                                    <i
                                        className="fas fa-times-circle"
                                        style={{ marginRight: "5px" }}
                                    ></i>
                                    Reset
                                </button>
                            )}
                        </div>

                        <div className="row g-3">
                            {products.map((p) => (
                                <div
                                    key={p.id}
                                    className="col-6 col-md-4 col-xl-3"
                                >
                                    <div
                                        className="card h-100 shadow-sm cursor-pointer"
                                        onClick={() =>
                                            this.addProductToCart(p.barcode)
                                        }
                                    >
                                        <img
                                            src={p.image_url}
                                            className="card-img-top"
                                            alt={p.name}
                                            style={{
                                                objectFit: "cover",
                                                height: "150px",
                                            }}
                                        />
                                        <div className="card-body p-2 text-center">
                                            <h6
                                                className={`card-title lh-sm ${
                                                    window.APP
                                                        .warning_quantity >
                                                    p.quantity
                                                        ? "text-danger"
                                                        : ""
                                                }`}
                                            >
                                                {p.name}
                                            </h6>
                                            <small className="text-muted">
                                                Stock: {p.quantity}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default Cart;

const root = document.getElementById("cart");
if (root) {
    const rootInstance = createRoot(root);
    rootInstance.render(<Cart />);
}

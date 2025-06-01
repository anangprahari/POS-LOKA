import React, { Component } from "react";
import { createRoot } from "react-dom/client";
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
            paymentMethod: "cash",
            discount: "",
            discountType: "fixed",
            note: "",
            showTransactionHistory: false,
            recentTransactions: [],
            cashAmount: "",
            showProductDetails: false,
            selectedProduct: null,
            holdOrders: [],
            selectedCustomer: null,
            loading: false,
            activeTab: "products",
        };

        // Binding methods
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
        this.handleDiscountChange = this.handleDiscountChange.bind(this);
        this.handleDiscountTypeChange =
        this.handleDiscountTypeChange.bind(this);
        this.handlePaymentMethodChange =
        this.handlePaymentMethodChange.bind(this);
        this.handleNoteChange = this.handleNoteChange.bind(this);
        this.toggleTransactionHistory =
        this.toggleTransactionHistory.bind(this);
        this.loadRecentTransactions = this.loadRecentTransactions.bind(this);
        this.handleCashAmountChange = this.handleCashAmountChange.bind(this);
        this.calculateChange = this.calculateChange.bind(this);
        this.printReceipt = this.printReceipt.bind(this);
        this.holdOrder = this.holdOrder.bind(this);
        this.retrieveHoldOrder = this.retrieveHoldOrder.bind(this);
        this.showProductDetails = this.showProductDetails.bind(this);
        this.applyItemDiscount = this.applyItemDiscount.bind(this);
        this.formatCurrency = this.formatCurrency.bind(this);
        this.formatNumber = this.formatNumber.bind(this);
        this.viewTransactionDetails = this.viewTransactionDetails.bind(this);
        this.closeProductDetails = this.closeProductDetails.bind(this);
        this.handleCancel = this.handleCancel.bind(this);
        this.handleCheckout = this.handleCheckout.bind(this);
        this.changeTab = this.changeTab.bind(this);
    }

    componentDidMount() {
        this.loadTranslations();
        this.loadCart();
        this.loadProducts();
        this.loadCustomers();
        this.loadRecentTransactions();
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(amount);
    }

    formatNumber(number) {
        return new Intl.NumberFormat("id-ID").format(number);
    }

    loadTranslations() {
        axios
            .get("/locale/cart")
            .then((res) => this.setState({ translations: res.data }))
            .catch((error) =>
                console.error("Error loading translations:", error)
            );
    }

    loadCustomers() {
        axios
            .get(`/customers`)
            .then((res) => this.setState({ customers: res.data }));
    }

    loadProducts(search = "") {
        const query = search ? `?search=${search}` : "";
        axios
            .get(`/products${query}`)
            .then((res) => this.setState({ products: res.data.data }));
    }

    loadCart() {
        axios
            .get("/cart")
            .then((response) => {
                this.setState({ cart: response.data });
            })
            .catch((error) => {
                console.error("Error loading cart:", error);
            });
    }

    handleOnChangeBarcode(e) {
        this.setState({ barcode: e.target.value });
    }

    handleScanBarcode(e) {
        e.preventDefault();
        const { barcode } = this.state;
        if (!barcode) return;

        this.setState({ loading: true });
        axios
            .post("/cart", { barcode })
            .then(() => {
                this.loadCart();
                this.setState({ barcode: "", loading: false });
            })
            .catch((err) => {
                this.setState({ loading: false });
                Swal.fire("Error!", err.response.data.message, "error");
            });
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
            .post("/cart/change-qty", { product_id, quantity: qty })
            .catch((err) =>
                Swal.fire("Error!", err.response.data.message, "error")
            );
    }

    getTotal(cart) {
        const subtotal = sum(cart.map((c) => c.pivot.quantity * c.price));
        let finalTotal = subtotal;
        if (this.state.discount > 0) {
            if (this.state.discountType === "fixed") {
                finalTotal = Math.max(0, subtotal - this.state.discount);
            } else {
                finalTotal = subtotal * (1 - this.state.discount / 100);
            }
        }
        return finalTotal;
    }

    getSubtotal(cart) {
        return sum(cart.map((c) => c.pivot.quantity * c.price));
    }

    handleClickDelete(product_id) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#e74c3c",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, remove it!",
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .delete("/cart/delete", { data: { product_id } })
                    .then(() =>
                        this.setState((state) => ({
                            cart: state.cart.filter((c) => c.id !== product_id),
                        }))
                    );
            }
        });
    }

    handleEmptyCart() {
        Swal.fire({
            title: this.state.translations.empty_cart_title || "Empty Cart?",
            text:
                this.state.translations.empty_cart_text ||
                "This will remove all items from your cart!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#e74c3c",
            cancelButtonColor: "#6c757d",
            confirmButtonText:
                this.state.translations.yes_empty || "Yes, empty cart!",
            cancelButtonText: this.state.translations.cancel || "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                // Loading state
                this.setState({ loading: true });

                axios
                    .post("/cart/empty")
                    .then((response) => {
                        this.setState({
                            cart: [],
                            loading: false,
                        });

                        // Reload cart to make sure it's synced
                        this.loadCart();

                        Swal.fire({
                            title:
                                this.state.translations.success || "Success!",
                            text:
                                this.state.translations.cart_emptied ||
                                "Cart has been emptied successfully!",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    })
                    .catch((error) => {
                        console.error("Error emptying cart:", error);
                        this.setState({ loading: false });

                        Swal.fire({
                            title: this.state.translations.error || "Error!",
                            text:
                                this.state.translations.empty_cart_error ||
                                "Failed to empty cart. Please try again.",
                            icon: "error",
                            confirmButtonText:
                                this.state.translations.ok || "OK",
                        });
                    });
            }
        });
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
            .post("/cart", { barcode })
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
        const customerId = e.target.value;
        const selectedCustomer = this.state.customers.find(
            (c) => c.id == customerId
        );
        this.setState({
            customer_id: customerId,
            selectedCustomer: selectedCustomer,
        });
    }

    handleClickSubmit() {
        if (this.state.cart.length === 0) {
            Swal.fire({
                title: this.state.translations["error"] || "Error",
                text:
                    this.state.translations["cart_empty"] ||
                    "Your cart is empty",
                icon: "error",
            });
            return;
        }

        if (
            this.state.paymentMethod === "cash" &&
            parseFloat(this.state.cashAmount) <
                parseFloat(this.getTotal(this.state.cart))
        ) {
            Swal.fire({
                title: this.state.translations["error"] || "Error",
                text:
                    this.state.translations["cash_amount_insufficient"] ||
                    "Cash amount is less than the total amount",
                icon: "error",
            });
            return;
        }

        this.setState({ loading: true });

        const orderData = {
            customer_id: this.state.customer_id || null,
            payment_method: this.state.paymentMethod,
            discount: this.state.discount,
            discount_type: this.state.discountType,
            note: this.state.note,
            amount:
                this.state.paymentMethod === "cash"
                    ? this.state.cashAmount
                    : this.getTotal(this.state.cart),
        };

        Swal.fire({
            title: this.state.translations["processing"] || "Processing...",
            text: this.state.translations["please_wait"] || "Please wait...",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        axios
            .post("/orders", orderData)
            .then((response) => {
                this.setState({ loading: false });
                Swal.close();

                if (response.data.success) {
                    const orderData = response.data.order;

                    Swal.fire({
                        title: this.state.translations["success"] || "Success",
                        text:
                            this.state.translations["order_success"] ||
                            "Order has been placed successfully!",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonText:
                            this.state.translations["print_receipt"] ||
                            "Print Receipt",
                        cancelButtonText:
                            this.state.translations["close"] || "Close",
                    }).then((result) => {
                        this.setState({
                            cart: [],
                            customer_id: "",
                            selectedCustomer: null,
                            paymentMethod: "cash",
                            discount: 0,
                            discountType: "fixed",
                            note: "",
                            cashAmount: 0,
                            search: "",
                        });

                        this.loadRecentTransactions();

                        if (result.isConfirmed && orderData) {
                            this.printReceipt(orderData);
                        }
                    });
                }
            })
            .catch((error) => {
                this.setState({ loading: false });
                Swal.close();
                console.error("Error submitting order:", error);

                let errorMessage =
                    this.state.translations["order_error"] ||
                    "There was an error processing your order";

                if (
                    error.response &&
                    error.response.data &&
                    error.response.data.errors
                ) {
                    const firstError = Object.values(
                        error.response.data.errors
                    )[0];
                    errorMessage = firstError[0] || errorMessage;
                }

                Swal.fire({
                    title: this.state.translations["error"] || "Error",
                    text: errorMessage,
                    icon: "error",
                });
            });
    }

    handleDiscountChange(e) {
        this.setState({ discount: parseFloat(e.target.value) || 0 });
    }

    handleDiscountTypeChange(e) {
        this.setState({ discountType: e.target.value });
    }

    handlePaymentMethodChange(e) {
        this.setState({ paymentMethod: e.target.value });
    }

    handleNoteChange(e) {
        this.setState({ note: e.target.value });
    }

    toggleTransactionHistory() {
        this.setState(
            (prevState) => ({
                showTransactionHistory: !prevState.showTransactionHistory,
            }),
            () => {
                if (this.state.showTransactionHistory) {
                    this.loadRecentTransactions();
                }
            }
        );
    }

    loadRecentTransactions() {
        axios
            .get("/orders/recent")
            .then((res) => {
                this.setState({ recentTransactions: res.data });
            })
            .catch((err) => {
                console.error("Error loading recent transactions:", err);
            });
    }

    handleCashAmountChange(e) {
        this.setState({ cashAmount: parseFloat(e.target.value) || 0 });
    }

    calculateChange() {
        const total = parseFloat(this.getTotal(this.state.cart));
        const cash = this.state.cashAmount;
        return cash >= total ? cash - total : 0;
    }

    printReceipt(orderData) {
        // Jika orderData kosong, gunakan data dari state
        if (!orderData) {
            // Calculate discount amount correctly
            let discountAmount = 0;
            if (this.state.discount > 0) {
                discountAmount =
                    this.state.discountType === "percentage"
                        ? (parseFloat(this.getSubtotal(this.state.cart)) *
                              this.state.discount) /
                          100
                        : parseFloat(this.state.discount);
            }

            // Calculate total with discount
            const total =
                parseFloat(this.getSubtotal(this.state.cart)) - discountAmount;

            orderData = {
                id: Math.floor(Math.random() * 1000), // Temporary ID
                created_at: new Date().toISOString(),
                order_items: this.state.cart.map((item) => ({
                    product: { name: item.name },
                    quantity: item.pivot.quantity,
                    price: item.price,
                })),
                customer: this.state.selectedCustomer,
                payment_method: this.state.paymentMethod,
                subtotal: this.getSubtotal(this.state.cart),
                discount: this.state.discount,
                discount_type: this.state.discountType,
                discount_amount: discountAmount,
                total: total,
                paid:
                    this.state.paymentMethod === "cash"
                        ? this.state.cashAmount
                        : total,
                change:
                    this.state.paymentMethod === "cash"
                        ? this.calculateChange()
                        : 0,
                note: this.state.note,
            };
        }

        // Buka window baru untuk cetak
        const receiptWindow = window.open("", "_blank", "width=400,height=600");

        if (receiptWindow) {
            // Format items
            let itemsHtml = "";
            if (orderData.order_items) {
                // Gunakan order_items dari orderData
                itemsHtml = orderData.order_items
                    .map(
                        (item) => `
                    <div class="item"> 
                        <div>${item.product.name} x ${item.quantity}</div> 
                        <div>${window.APP.currency_symbol} ${(
                            parseFloat(item.price) * item.quantity
                        ).toFixed(2)}</div> 
                    </div> 
                `
                    )
                    .join("");
            } else if (this.state.cart) {
                // Fallback ke keranjang di state
                itemsHtml = this.state.cart
                    .map(
                        (item) => `
                    <div class="item"> 
                        <div>${item.name} x ${item.pivot.quantity}</div> 
                        <div>${window.APP.currency_symbol} ${(
                            item.price * item.pivot.quantity
                        ).toFixed(2)}</div> 
                    </div> 
                `
                    )
                    .join("");
            }

            // Format tanggal
            const orderDate = orderData.created_at
                ? new Date(orderData.created_at).toLocaleString()
                : new Date().toLocaleString();

            // Format customer name
            const customerName = orderData.customer
                ? `${orderData.customer.first_name} ${orderData.customer.last_name}`
                : this.state.translations["general_customer"] ||
                  "General Customer";

            // Format payment method
            const paymentMethod =
                orderData.payment_method || this.state.paymentMethod;

            // Format subtotal
            const subtotal = orderData.subtotal
                ? parseFloat(orderData.subtotal).toFixed(2)
                : this.getSubtotal(this.state.cart);

            // Format discount
            const hasDiscount =
                orderData.discount > 0 || this.state.discount > 0;

            // Hitung diskon dan total akhir
            let discountAmount = 0;
            let discountType = "fixed";

            // Use the discount_amount directly from orderData if available
            if (orderData.discount_amount !== undefined) {
                discountAmount = parseFloat(orderData.discount_amount);
                discountType = orderData.discount_type;
            } else if (orderData.discount > 0) {
                discountAmount =
                    orderData.discount_type === "percentage"
                        ? (parseFloat(orderData.subtotal) *
                              orderData.discount) /
                          100
                        : parseFloat(orderData.discount);
                discountType = orderData.discount_type;
            } else if (this.state.discount > 0) {
                discountAmount =
                    this.state.discountType === "percentage"
                        ? (parseFloat(subtotal) * this.state.discount) / 100
                        : parseFloat(this.state.discount);
                discountType = this.state.discountType;
            }

            // If total is provided in orderData, use it directly
            const total = orderData.total
                ? parseFloat(orderData.total).toFixed(2)
                : (parseFloat(subtotal) - parseFloat(discountAmount)).toFixed(
                      2
                  );

            // Format paid amount and change
            const paidAmount = orderData.paid
                ? parseFloat(orderData.paid).toFixed(2)
                : this.state.paymentMethod === "cash"
                ? parseFloat(this.state.cashAmount).toFixed(2)
                : total;

            // Use change directly from orderData if available
            const change =
                orderData.change !== undefined
                    ? parseFloat(orderData.change).toFixed(2)
                    : paymentMethod === "cash"
                    ? (parseFloat(paidAmount) - parseFloat(total)).toFixed(2)
                    : "0.00";

            // Membuat konten struk
            const receiptContent = `
                <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        body { font-family: monospace; font-size: 12px; width: 300px; margin: 0 auto; }
                        .header, .footer { text-align: center; margin: 10px 0; }
                        .divider { border-top: 1px dashed #000; margin: 5px 0; }
                        .item { display: flex; justify-content: space-between; margin: 5px 0; }
                        .total { font-weight: bold; margin-top: 10px; }
                        .customer-info { margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>${window.APP.store_name || "POS KOPI LOKA"}</h2>
                        <p>${window.APP.store_address || "Jambi"}</p>
                        <p>Tel: ${window.APP.store_phone || "089529717594"}</p>
                        <p>Date: ${orderDate}</p>
                        <p>Receipt #: ${orderData.id || ""}</p>
                    </div>
                    
                    <div class="customer-info">
                        <p>Customer: ${customerName}</p>
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div>
                        ${itemsHtml}
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="total">
                        <div class="item">
                            <div>Subtotal:</div>
                            <div>${window.APP.currency_symbol} ${subtotal}</div>
                        </div>
                        ${
                            hasDiscount
                                ? `
                            <div class="item">
                                <div>Discount ${
                                    discountType === "percentage"
                                        ? `(${
                                              orderData.discount ||
                                              this.state.discount
                                          }%)`
                                        : "(Fixed)"
                                }:</div>
                                <div>${window.APP.currency_symbol} ${parseFloat(
                                      discountAmount
                                  ).toFixed(2)}</div>
                            </div>
                        `
                                : ""
                        }
                        <div class="item">
                            <div>Total:</div>
                            <div>${window.APP.currency_symbol} ${total}</div>
                        </div>
                        ${
                            paymentMethod === "cash"
                                ? `
                            <div class="item">
                                <div>Cash:</div>
                                <div>${window.APP.currency_symbol} ${paidAmount}</div>
                            </div>
                            <div class="item">
                                <div>Change:</div>
                                <div>${window.APP.currency_symbol} ${change}</div>
                            </div>
                        `
                                : `
                            <div class="item">
                                <div>Payment Method:</div>
                                <div>${paymentMethod.toUpperCase()}</div>
                            </div>
                        `
                        }
                    </div>
                    
                    <div class="divider"></div>
                    
                    <div class="footer">
                        <p>Thank you for your purchase!</p>
                        ${
                            orderData.note
                                ? `<p>Note: ${orderData.note}</p>`
                                : ""
                        }
                        ${
                            window.APP.receipt_footer
                                ? `<p>${window.APP.receipt_footer}</p>`
                                : ""
                        }
                    </div>
                    
                    <script>
                        window.onload = function() {
                            window.print();
                        }
                    </script>
                </body>
                </html>
            `;

            // Menulis konten ke window dan menutupnya
            receiptWindow.document.write(receiptContent);
            receiptWindow.document.close();
        }
    }

    holdOrder() {
        if (this.state.cart.length === 0) {
            Swal.fire("Error", "Cart is empty", "error");
            return;
        }

        Swal.fire({
            title: "Hold Order",
            input: "text",
            inputLabel: "Enter a reference name for this order",
            inputPlaceholder: "e.g., Table 5, John Smith",
            showCancelButton: true,
            confirmButtonText: "Hold",
            showLoaderOnConfirm: true,
            preConfirm: (reference) => {
                if (!reference) {
                    Swal.showValidationMessage("Please enter a reference name");
                    return false;
                }

                const heldOrder = {
                    reference,
                    cart: [...this.state.cart],
                    customer_id: this.state.customer_id,
                    discount: this.state.discount,
                    discountType: this.state.discountType,
                    note: this.state.note,
                    timestamp: new Date().toISOString(),
                };

                const updatedHoldOrders = [...this.state.holdOrders, heldOrder];
                this.setState({ holdOrders: updatedHoldOrders });

                try {
                    localStorage.setItem(
                        "holdOrders",
                        JSON.stringify(updatedHoldOrders)
                    );
                } catch (e) {
                    console.error(
                        "Error saving held orders to localStorage:",
                        e
                    );
                }

                return heldOrder;
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.isConfirmed) {
                this.handleEmptyCart();
                Swal.fire(
                    "Order Held",
                    `Order has been held with reference: ${result.value.reference}`,
                    "success"
                );
            }
        });
    }

    retrieveHoldOrder() {
        if (this.state.cart.length > 0) {
            Swal.fire({
                title: "Warning",
                text: "Loading a held order will clear your current cart. Continue?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, continue",
            }).then((result) => {
                if (result.isConfirmed) {
                    this.showHeldOrders();
                }
            });
        } else {
            this.showHeldOrders();
        }
    }

    showHeldOrders() {
        let holdOrders = this.state.holdOrders;
        try {
            const storedOrders = localStorage.getItem("holdOrders");
            if (storedOrders) {
                holdOrders = JSON.parse(storedOrders);
                this.setState({ holdOrders });
            }
        } catch (e) {
            console.error("Error loading held orders from localStorage:", e);
        }

        if (holdOrders.length === 0) {
            Swal.fire("No Held Orders", "There are no orders on hold.", "info");
            return;
        }

        Swal.fire({
            title: "Retrieve Held Order",
            html: `
                <select id="held-order-select" class="swal2-select" style="width: 100%">
                    ${holdOrders
                        .map(
                            (order, index) =>
                                `<option value="${index}">${
                                    order.reference
                                } - ${new Date(
                                    order.timestamp
                                ).toLocaleTimeString()}</option>`
                        )
                        .join("")}
                </select>
            `,
            showCancelButton: true,
            confirmButtonText: "Retrieve",
            preConfirm: () => {
                const selectEl = document.getElementById("held-order-select");
                return selectEl ? parseInt(selectEl.value) : 0;
            },
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedOrder = holdOrders[result.value];

                this.setState({
                    cart: selectedOrder.cart,
                    customer_id: selectedOrder.customer_id || "",
                    discount: selectedOrder.discount || 0,
                    discountType: selectedOrder.discountType || "fixed",
                    note: selectedOrder.note || "",
                });

                const updatedHoldOrders = holdOrders.filter(
                    (_, idx) => idx !== result.value
                );
                this.setState({ holdOrders: updatedHoldOrders });

                try {
                    localStorage.setItem(
                        "holdOrders",
                        JSON.stringify(updatedHoldOrders)
                    );
                } catch (e) {
                    console.error(
                        "Error updating held orders in localStorage:",
                        e
                    );
                }

                Swal.fire(
                    "Order Retrieved",
                    `Order "${selectedOrder.reference}" has been loaded.`,
                    "success"
                );
            }
        });
    }

    showProductDetails(product) {
        this.setState({
            showProductDetails: true,
            selectedProduct: product,
        });
    }

    closeProductDetails() {
        this.setState({
            showProductDetails: false,
            selectedProduct: null,
        });
    }

    applyItemDiscount(product_id) {
        const product = this.state.cart.find((p) => p.id === product_id);
        if (!product) return;

        Swal.fire({
            title: "Apply Discount",
            html: `
                <div style="text-align: left; margin-bottom: 15px;">
                    <p>Product: ${product.name}</p>
                    <p>Price: ${window.APP.currency_symbol} ${product.price}</p>
                </div>
                <div style="display: flex; margin-bottom: 10px;">
                    <input id="discount-amount" type="number" min="0" class="swal2-input" placeholder="Amount" style="margin-right: 10px; width: 60%;">
                    <select id="discount-type" class="swal2-select" style="width: 40%;">
                        <option value="fixed">Fixed</option>
                        <option value="percentage">Percentage</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: "Apply",
            preConfirm: () => {
                const discountAmount =
                    document.getElementById("discount-amount").value;
                const discountType =
                    document.getElementById("discount-type").value;

                if (
                    !discountAmount ||
                    isNaN(parseFloat(discountAmount)) ||
                    parseFloat(discountAmount) < 0
                ) {
                    Swal.showValidationMessage(
                        "Please enter a valid discount amount"
                    );
                    return false;
                }

                return {
                    amount: parseFloat(discountAmount),
                    type: discountType,
                };
            },
        }).then((result) => {
            if (result.isConfirmed) {
                const updatedCart = this.state.cart.map((item) => {
                    if (item.id === product_id) {
                        const discount =
                            result.value.type === "percentage"
                                ? item.price * (result.value.amount / 100)
                                : result.value.amount;

                        const discountedPrice = Math.max(
                            0,
                            item.price - discount
                        );

                        return {
                            ...item,
                            original_price: item.original_price || item.price,
                            price: discountedPrice,
                            discount_info: {
                                amount: result.value.amount,
                                type: result.value.type,
                            },
                        };
                    }
                    return item;
                });

                this.setState({ cart: updatedCart });

                Swal.fire(
                    "Discount Applied",
                    "Item discount has been applied.",
                    "success"
                );
            }
        });
    }

    handleCancel = () => {
    console.log('Cancel button clicked');
    console.log('Current cart:', this.state.cart);
    this.handleEmptyCart();
};

    handleCheckout = () => {
        this.handleClickSubmit();
    };

    changeTab(tab) {
        this.setState({ activeTab: tab });
    }

    render() {
        const {
            cart,
            products,
            customers,
            barcode,
            translations,
            search,
            paymentMethod,
            discount,
            discountType,
            note,
            showTransactionHistory,
            recentTransactions,
            cashAmount,
            showProductDetails,
            selectedProduct,
            activeTab,
            loading,
        } = this.state;

        const changeAmount = this.calculateChange();
        const subtotal = this.getSubtotal(cart);
        const total = this.getTotal(cart);

        return (
            <div className="pos-container">
                {/* Global Styles */}
                <style jsx global>{`
                    :root {
                        --primary: #ff7b25; /* Orange primary color */
                        --primary-light: #fff0e8;
                        --primary-dark: #e05d00;
                        --secondary: #3a86ff;
                        --success: #4cc9f0;
                        --danger: #f72585;
                        --warning: rgb(182, 200, 21);
                        --info: #4895ef;
                        --light: #f8f9fa;
                        --dark: #212529;
                        --gray: #6c757d;
                        --border-radius: 8px;
                        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                        --transition: all 0.2s ease;
                    }

                    .pos-container {
                        font-family: "Inter", -apple-system, BlinkMacSystemFont,
                            sans-serif;
                        background-color: #f5f7fa;
                        min-height: 100vh;
                        padding: 1rem;
                    }

                    .card {
                        border: none;
                        border-radius: var(--border-radius);
                        box-shadow: var(--box-shadow);
                        transition: var(--transition);
                        background-color: white;
                    }

                    .card:hover {
                        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
                    }

                    .btn {
                        border-radius: var(--border-radius);
                        font-weight: 500;
                        transition: var(--transition);
                        padding: 0.5rem 1rem;
                        border: none;
                        position: relative;
                        overflow: hidden;
                    }

                    .btn:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
                    }

                    .btn:active {
                        transform: translateY(0);
                    }

                    /* Primary Button */
                    .btn-primary {
                        background: linear-gradient(
                            135deg,
                            var(--primary) 0%,
                            var(--primary-dark) 100%
                        );
                        color: white;
                    }

                    .btn-primary:hover {
                        background: linear-gradient(
                            135deg,
                            var(--primary-dark) 0%,
                            #cc5500 100%
                        );
                        box-shadow: 0 4px 12px rgba(255, 123, 37, 0.3);
                    }

                    /* Secondary Button */
                    .btn-secondary {
                        background: linear-gradient(
                            135deg,
                            #6c757d 0%,
                            #495057 100%
                        );
                        color: white;
                    }

                    .btn-secondary:hover {
                        background: linear-gradient(
                            135deg,
                            #495057 0%,
                            #343a40 100%
                        );
                        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
                    }

                    /* Success Button */
                    .btn-success {
                        background: linear-gradient(
                            135deg,
                            #28a745 0%,
                            #218838 100%
                        );
                        color: white;
                    }

                    .btn-success:hover {
                        background: linear-gradient(
                            135deg,
                            #218838 0%,
                            #1e7e34 100%
                        );
                        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
                    }

                    /* Danger Button */
                    .btn-danger {
                        background: linear-gradient(
                            135deg,
                            #dc3545 0%,
                            #c82333 100%
                        );
                        color: white;
                    }

                    .btn-danger:hover {
                        background: linear-gradient(
                            135deg,
                            #c82333 0%,
                            #bd2130 100%
                        );
                        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
                    }

                    /* Warning Button */
                    .btn-warning {
                        background: linear-gradient(
                            135deg,
                            #ffc107 0%,
                            #e0a800 100%
                        );
                        color: #212529;
                    }

                    .btn-warning:hover {
                        background: linear-gradient(
                            135deg,
                            #e0a800 0%,
                            #d39e00 100%
                        );
                        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
                    }

                    /* Info Button */
                    .btn-info {
                        background: linear-gradient(
                            135deg,
                            #17a2b8 0%,
                            #138496 100%
                        );
                        color: white;
                    }

                    .btn-info:hover {
                        background: linear-gradient(
                            135deg,
                            #138496 0%,
                            #117a8b 100%
                        );
                        box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
                    }

                    /* Outline Buttons */
                    .btn-outline-primary {
                        background: transparent;
                        border: 1px solid var(--primary);
                        color: var(--primary);
                    }

                    .btn-outline-primary:hover {
                        background: rgba(255, 123, 37, 0.1);
                    }

                    /* Hold Button - Soft Orange */
                    .btn-hold {
                        background: linear-gradient(
                            135deg,
                            #ffd166 0%,
                            /* Soft yellow-orange */ #f4a261 100%
                                /* Muted orange */
                        );
                        color: #333; /* Dark text for contrast */
                    }

                    .btn-hold:hover {
                        background: linear-gradient(
                            135deg,
                            #f4a261 0%,
                            #e76f51 100%
                        );
                    }

                    /* Retrieve Button - Neutral Gray */
                    .btn-retrieve {
                        background: linear-gradient(
                            135deg,
                            #b8b8b8 0%,
                            /* Light gray */ #8d8d8d 100% /* Medium gray */
                        );
                        color: white;
                    }

                    .btn-retrieve:hover {
                        background: linear-gradient(
                            135deg,
                            #8d8d8d 0%,
                            #6c6c6c 100%
                        );
                    }

                    /* Disabled Buttons */
                    .btn:disabled {
                        opacity: 0.65;
                        transform: none !important;
                        box-shadow: none !important;
                    }

                    /* Button Icons */
                    .btn i {
                        margin-right: 5px;
                    }

                    .btn-orange {
                        background: linear-gradient(
                            135deg,
                            var(--primary) 0%,
                            var(--primary-dark) 100%
                        );
                        color: white;
                        border: 1px solid var(--primary);
                    }

                    .btn-orange:hover {
                        background: linear-gradient(
                            135deg,
                            var(--primary-dark) 0%,
                            #cc5500 100%
                        );
                        box-shadow: 0 4px 12px rgba(255, 123, 37, 0.3);
                    }

                    .btn-outline-orange {
                        background: transparent;
                        color: var(--primary);
                        border: 1px solid var(--primary);
                    }

                    .btn-outline-orange:hover {
                        background: rgba(255, 123, 37, 0.1);
                    }

                    .badge {
                        font-weight: 500;
                        padding: 0.35em 0.65em;
                        border-radius: 50px;
                    }
                    .badge.bg-primary {
                        background-color: var(--primary) !important;
                    }

                    .form-control,
                    .form-select {
                        border-radius: var(--border-radius);
                        padding: 0.5rem 0.75rem;
                        border: 1px solid #ced4da;
                        transition: var(--transition);
                    }

                    .form-control:focus,
                    .form-select:focus {
                        border-color: var(--primary);
                        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
                    }

                    .nav-tabs {
                        border-bottom: 2px solid #dee2e6;
                    }

                    .nav-tabs .nav-link {
                        border: none;
                        color: var(--gray);
                        font-weight: 500;
                        padding: 0.75rem 1.5rem;
                        border-radius: 0;
                        margin-right: 0.5rem;
                    }

                    .nav-tabs .nav-link.active {
                        color: var(--primary);
                        background-color: transparent;
                        border-bottom: 2px solid var(--primary);
                    }

                    .nav-tabs .nav-link:hover:not(.active) {
                        color: var(--primary);
                        border-bottom: 2px solid var(--primary-light);
                    }

                    .products-grid {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 1rem;
                        padding: 8px;
                    }

                    .product-card {
                        border: 1px solid #f0f0f0;
                        border-radius: var(--border-radius);
                        overflow: hidden;
                        transition: var(--transition);
                        background: white;
                    }

                    .product-card:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 6px 12px rgba(255, 123, 37, 0.15);
                    }

                    .product-img-container {
                        position: relative;
                        height: 140px;
                        overflow: hidden;
                    }

                    .product-img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                        transition: transform 0.3s ease;
                    }

                    .product-card:hover .product-img {
                        transform: scale(1.05);
                    }

                    .product-barcode {
                        position: absolute;
                        top: 8px;
                        left: 8px;
                        background: rgba(255, 255, 255, 0.9);
                        padding: 2px 6px;
                        border-radius: 4px;
                        font-size: 11px;
                        font-family: "Courier New", monospace;
                        color: var(--dark);
                    }

                    .product-stock-badge {
                        position: absolute;
                        top: 8px;
                        right: 8px;
                        font-size: 11px;
                        padding: 2px 6px;
                    }

                    .product-info {
                        padding: 12px;
                    }

                    .product-name {
                        font-size: 14px;
                        font-weight: 600;
                        margin-bottom: 8px;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }

                    .product-details {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    }

                    .product-price {
                        font-weight: 700;
                        color: var(--primary);
                        font-size: 14px;
                    }

                    .product-stock {
                        font-size: 12px;
                        color: var(--gray);
                    }

                    .cart-item {
                        transition: background-color 0.2s ease;
                        padding: 0.75rem 0;
                        border-bottom: 1px solid #f0f0f0;
                    }

                    .cart-item:last-child {
                        border-bottom: none;
                    }

                    .cart-item:hover {
                        background-color: var(--primary-light);
                    }

                    .qty-input {
                        width: 60px;
                        text-align: center;
                        border-radius: var(--border-radius);
                        border: 1px solid #ced4da;
                        padding: 0.25rem;
                    }

                    .discount-badge {
                        background-color: #f0f7ff;
                        color: var(--primary);
                        font-size: 0.75rem;
                    }

                    .modal-overlay {
                        position: fixed;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-color: rgba(0, 0, 0, 0.5);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 1050;
                    }

                    .modal-content {
                        background-color: white;
                        border-radius: var(--border-radius);
                        width: 90%;
                        max-width: 800px;
                        max-height: 90vh;
                        overflow-y: auto;
                        animation: modalFadeIn 0.3s ease;
                    }

                    @keyframes modalFadeIn {
                        from {
                            opacity: 0;
                            transform: translateY(-20px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }

                    .close-btn {
                        font-size: 1.5rem;
                        color: var(--gray);
                        cursor: pointer;
                        transition: color 0.2s ease;
                    }

                    .close-btn:hover {
                        color: var(--danger);
                    }
                    .text-primary {
                        color: var(--primary) !important;
                    }

                    .loading-spinner {
                        display: inline-block;
                        width: 1.5rem;
                        height: 1.5rem;
                        border: 3px solid rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        border-top-color: white;
                        animation: spin 1s ease-in-out infinite;
                    }

                    @keyframes spin {
                        to {
                            transform: rotate(360deg);
                        }
                    }

                    @media (max-width: 768px) {
                        .pos-container {
                            padding: 0.5rem;
                        }

                        .nav-tabs .nav-link {
                            padding: 0.5rem 1rem;
                            font-size: 0.875rem;
                        }
                    }

                    /* Payment Method Badge Colors - Improved Contrast */
                    .payment-cash {
                        background: linear-gradient(
                            135deg,
                            #10b981 0%,
                            #059669 100%
                        );
                        color: white;
                        border: 1px solid #059669;
                    }

                    .payment-card {
                        background: linear-gradient(
                            135deg,
                            #3b82f6 0%,
                            #1d4ed8 100%
                        );
                        color: white;
                        border: 1px solid #1d4ed8;
                    }

                    .payment-bank {
                        background: linear-gradient(
                            135deg,
                            #f59e0b 0%,
                            #d97706 100%
                        );
                        color: white;
                        border: 1px solid #d97706;
                    }

                    .payment-ewallet {
                        background: linear-gradient(
                            135deg,
                            #8b5cf6 0%,
                            #7c3aed 100%
                        );
                        color: white;
                        border: 1px solid #7c3aed;
                    }

                    /* Hover Effects */
                    .payment-cash:hover {
                        background: linear-gradient(
                            135deg,
                            #059669 0%,
                            #047857 100%
                        );
                        transform: translateY(-1px);
                        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
                    }

                    .payment-card:hover {
                        background: linear-gradient(
                            135deg,
                            #1d4ed8 0%,
                            #1e40af 100%
                        );
                        transform: translateY(-1px);
                        box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
                    }

                    .payment-bank:hover {
                        background: linear-gradient(
                            135deg,
                            #d97706 0%,
                            #b45309 100%
                        );
                        transform: translateY(-1px);
                        box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
                    }

                    .payment-ewallet:hover {
                        background: linear-gradient(
                            135deg,
                            #7c3aed 0%,
                            #6d28d9 100%
                        );
                        transform: translateY(-1px);
                        box-shadow: 0 4px 8px rgba(139, 92, 246, 0.3);
                    }
                `}</style>

                {/* Main Layout */}
                <div className="container-fluid">
                    {/* Header */}
                    <div className="row mb-4">
                        <div className="col-12">
                            <div className="card p-3">
                                <div className="d-flex justify-content-between align-items-center">
                                    <h2 className="mb-0 text-primary">
                                        {window.APP.store_name || "POS System"}
                                    </h2>
                                    <div className="d-flex gap-2">
                                        <button
                                            className={`btn mr-2 ${
                                                showTransactionHistory
                                                    ? "btn-outline-orange" // Ketika aktif
                                                    : "btn-orange" // Ketika tidak aktif
                                            }`}
                                            onClick={() =>
                                                this.setState({
                                                    showTransactionHistory: false,
                                                })
                                            }
                                        >
                                            <i className="fas fa-shopping-cart me-2"></i>
                                            {translations["pos"] ||
                                                "Point of Sale"}
                                        </button>
                                        <button
                                            className={`btn ${
                                                showTransactionHistory
                                                    ? "btn-orange" // Ketika aktif
                                                    : "btn-outline-orange" // Ketika tidak aktif
                                            }`}
                                            onClick={
                                                this.toggleTransactionHistory
                                            }
                                        >
                                            <i className="fas fa-history me-2"></i>
                                            {translations["transactions"] ||
                                                "Transactions"}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {showTransactionHistory ? (
                        /* Transactions View */
                        <div className="row">
                            <div className="col-12">
                                <div className="card">
                                    <div className="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                                        <h5 className="mb-0 text-dark">
                                            <i className="fas fa-history me-2 text-primary mr-2"></i>
                                            {translations[
                                                "recent_transactions"
                                            ] || "Recent Transactions"}
                                        </h5>
                                        <button
                                            className="btn btn-sm btn-outline-primary"
                                            onClick={
                                                this.loadRecentTransactions
                                            }
                                        >
                                            <i className="fas fa-sync-alt me-1 mr-2"></i>
                                            {translations["refresh"] ||
                                                "Refresh"}
                                        </button>
                                    </div>
                                    <div className="card-body p-0">
                                        <div className="table-responsive">
                                            <table className="table table-hover mb-0">
                                                <thead className="bg-light">
                                                    <tr>
                                                        <th className="border-0">
                                                            #
                                                        </th>
                                                        <th className="border-0">
                                                            {translations[
                                                                "date"
                                                            ] || "Date"}
                                                        </th>
                                                        <th className="border-0">
                                                            {translations[
                                                                "customer"
                                                            ] || "Customer"}
                                                        </th>
                                                        <th className="border-0">
                                                            {translations[
                                                                "payment_method"
                                                            ] || "Payment"}
                                                        </th>
                                                        <th className="border-0 text-end">
                                                            {translations[
                                                                "total"
                                                            ] || "Total"}
                                                        </th>
                                                        <th className="border-0 text-center">
                                                            {translations[
                                                                "actions"
                                                            ] || "Actions"}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {recentTransactions.length >
                                                    0 ? (
                                                        recentTransactions.map(
                                                            (transaction) => (
                                                                <tr
                                                                    key={
                                                                        transaction.id
                                                                    }
                                                                    className="border-top"
                                                                >
                                                                    <td>
                                                                        #
                                                                        {
                                                                            transaction.invoice_number
                                                                        }
                                                                    </td>
                                                                    <td>
                                                                        {new Date(
                                                                            transaction.created_at
                                                                        ).toLocaleString()}
                                                                    </td>
                                                                    <td>
                                                                        {transaction.customer
                                                                            ? `${transaction.customer.first_name} ${transaction.customer.last_name}`
                                                                            : translations[
                                                                                  "general_customer"
                                                                              ] ||
                                                                              "General Customer"}
                                                                    </td>
                                                                    <td>
                                                                        <span
                                                                            className={`badge payment-${
                                                                                transaction.payment_method ===
                                                                                "cash"
                                                                                    ? "cash"
                                                                                    : transaction.payment_method ===
                                                                                      "card"
                                                                                    ? "card"
                                                                                    : transaction.payment_method ===
                                                                                      "bank_transfer"
                                                                                    ? "bank"
                                                                                    : "ewallet"
                                                                            }`}
                                                                        >
                                                                            {transaction.payment_method.toUpperCase()}
                                                                        </span>
                                                                    </td>
                                                                    <td className="text-end fw-bold">
                                                                        {
                                                                            window
                                                                                .APP
                                                                                .currency_symbol
                                                                        }{" "}
                                                                        {parseFloat(
                                                                            transaction.total
                                                                        ).toFixed(
                                                                            2
                                                                        )}
                                                                    </td>
                                                                    <td className="text-center">
                                                                        <div className="btn-group btn-group-sm">
                                                                            <button
                                                                                className="btn btn-outline-primary btn-sm"
                                                                                onClick={() =>
                                                                                    this.viewTransactionDetails(
                                                                                        transaction.id
                                                                                    )
                                                                                }
                                                                                title={
                                                                                    translations[
                                                                                        "view_details"
                                                                                    ] ||
                                                                                    "View Details"
                                                                                }
                                                                            >
                                                                                <i className="fas fa-eye"></i>
                                                                            </button>
                                                                            <button
                                                                                className="btn btn-outline-success btn-sm"
                                                                                onClick={() =>
                                                                                    this.printReceipt(
                                                                                        transaction
                                                                                    )
                                                                                }
                                                                                title={
                                                                                    translations[
                                                                                        "print_receipt"
                                                                                    ] ||
                                                                                    "Print Receipt"
                                                                                }
                                                                            >
                                                                                <i className="fas fa-print"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            )
                                                        )
                                                    ) : (
                                                        <tr>
                                                            <td
                                                                colSpan="6"
                                                                className="text-center py-4 text-muted"
                                                            >
                                                                <i className="fas fa-inbox fa-2x mb-2"></i>
                                                                <p>
                                                                    {translations[
                                                                        "no_transactions"
                                                                    ] ||
                                                                        "No transactions found"}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    )}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ) : (
                        /* POS View */
                        <div className="row">
                            {/* Cart Sidebar */}
                            <div className="col-lg-4 mb-4">
                                <div className="card h-100">
                                    <div className="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                                        <h5 className="mb-0 text-dark">
                                            <i className="fas fa-shopping-cart me-2 text-primary mr-2"></i>
                                            {translations["cart"] || "Cart"}
                                        </h5>
                                        <span className="badge bg-primary">
                                            {cart.length}{" "}
                                            {translations["items"] || "items"}
                                        </span>
                                    </div>

                                    <div className="card-body d-flex flex-column p-0">
                                        {/* Customer and Barcode Section */}
                                        <div className="p-3 border-bottom">
                                            <div className="mb-3">
                                                <label className="form-label small text-muted mb-1 mr-2">
                                                    {translations["customer"] ||
                                                        "Customer"}
                                                </label>
                                                <select
                                                    className="form-select"
                                                    onChange={
                                                        this.setCustomerId
                                                    }
                                                    value={
                                                        this.state.customer_id
                                                    }
                                                >
                                                    <option value="">
                                                        {translations[
                                                            "general_customer"
                                                        ] || "General Customer"}
                                                    </option>
                                                    {customers.map((cus) => (
                                                        <option
                                                            key={cus.id}
                                                            value={cus.id}
                                                        >
                                                            {cus.first_name}{" "}
                                                            {cus.last_name}
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>

                                            <form
                                                onSubmit={
                                                    this.handleScanBarcode
                                                }
                                                className="mb-0"
                                            >
                                                <label className="form-label small text-muted mb-1">
                                                    {translations[
                                                        "scan_barcode"
                                                    ] || "Scan Barcode"}
                                                </label>
                                                <div className="input-group">
                                                    <input
                                                        type="text"
                                                        className="form-control mr-2"
                                                        placeholder={
                                                            translations[
                                                                "scan_barcode_placeholder"
                                                            ] || "Enter barcode"
                                                        }
                                                        value={barcode}
                                                        onChange={
                                                            this
                                                                .handleOnChangeBarcode
                                                        }
                                                    />
                                                    <button
                                                        className="btn btn-primary"
                                                        type="submit"
                                                        disabled={loading}
                                                    >
                                                        {loading ? (
                                                            <span className="loading-spinner"></span>
                                                        ) : (
                                                            <i className="fas fa-barcode"></i>
                                                        )}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        {/* Cart Items */}
                                        <div
                                            className="flex-grow-1 overflow-auto"
                                            style={{ maxHeight: "400px" }}
                                        >
                                            {cart.length > 0 ? (
                                                <div className="p-3">
                                                    {cart.map((item) => (
                                                        <div
                                                            key={item.id}
                                                            className="cart-item border-bottom pb-3 mb-3"
                                                        >
                                                            {/* Header: Item Name and Total Price */}
                                                            <div className="d-flex justify-content-between align-items-center mb-2">
                                                                <div className="flex-grow-1">
                                                                    <h6
                                                                        className="mb-0 fw-medium text-truncate"
                                                                        style={{
                                                                            maxWidth:
                                                                                "200px",
                                                                        }}
                                                                    >
                                                                        {
                                                                            item.name
                                                                        }
                                                                    </h6>
                                                                </div>
                                                                <div className="text-end">
                                                                    <div className="fw-bold fs-6 text-primary">
                                                                        {
                                                                            window
                                                                                .APP
                                                                                .currency_symbol
                                                                        }
                                                                        {(
                                                                            item.price *
                                                                            item
                                                                                .pivot
                                                                                .quantity
                                                                        ).toFixed(
                                                                            2
                                                                        )}
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {/* Body: Quantity Controls and Unit Price */}
                                                            <div className="d-flex justify-content-between align-items-center">
                                                                {/* Left side: Quantity and Remove button */}
                                                                <div className="d-flex align-items-center gap-2">
                                                                    <div className="d-flex align-items-center">
                                                                        <label className="form-label mb-0 me-2 small text-muted">
                                                                            Qty:
                                                                        </label>
                                                                        <input
                                                                            type="number"
                                                                            min="1"
                                                                            className="form-control form-control-sm"
                                                                            style={{
                                                                                width: "70px",
                                                                            }}
                                                                            value={
                                                                                item
                                                                                    .pivot
                                                                                    .quantity
                                                                            }
                                                                            onChange={(
                                                                                e
                                                                            ) =>
                                                                                this.handleChangeQty(
                                                                                    item.id,
                                                                                    e
                                                                                        .target
                                                                                        .value
                                                                                )
                                                                            }
                                                                        />
                                                                    </div>
                                                                    <button
                                                                        className="btn btn-sm btn-outline-danger d-flex align-items-center gap-1"
                                                                        onClick={() =>
                                                                            this.handleClickDelete(
                                                                                item.id
                                                                            )
                                                                        }
                                                                        title={
                                                                            translations[
                                                                                "remove"
                                                                            ] ||
                                                                            "Remove"
                                                                        }
                                                                    >
                                                                        <i className="fas fa-trash fa-xs"></i>
                                                                        <span className="d-none d-md-inline small">
                                                                            Remove
                                                                        </span>
                                                                    </button>
                                                                </div>

                                                                {/* Right side: Unit price */}
                                                                <div className="text-end">
                                                                    <div className="small text-muted">
                                                                        {
                                                                            window
                                                                                .APP
                                                                                .currency_symbol
                                                                        }
                                                                        {parseFloat(
                                                                            item.price
                                                                        ).toFixed(
                                                                            2
                                                                        )}{" "}
                                                                        per item
                                                                    </div>
                                                                    <div className="small text-muted">
                                                                        ×{" "}
                                                                        {
                                                                            item
                                                                                .pivot
                                                                                .quantity
                                                                        }{" "}
                                                                        {item
                                                                            .pivot
                                                                            .quantity >
                                                                        1
                                                                            ? "items"
                                                                            : "item"}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            ) : (
                                                <div className="text-center py-5 text-muted">
                                                    <i className="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                                                    <p>
                                                        {translations[
                                                            "cart_empty"
                                                        ] ||
                                                            "Your cart is empty"}
                                                    </p>
                                                </div>
                                            )}
                                        </div>

                                        {/* Order Summary */}
                                        {cart.length > 0 && (
                                            <div className="border-top p-3 bg-light">
                                                <div className="mb-3">
                                                    <label className="form-label small text-muted mb-1">
                                                        {translations[
                                                            "order_discount"
                                                        ] || "Order Discount"}
                                                    </label>
                                                    <div className="row g-2">
                                                        <div className="col-8">
                                                            <input
                                                                type="number"
                                                                min="0"
                                                                className="form-control"
                                                                value={discount}
                                                                onChange={
                                                                    this
                                                                        .handleDiscountChange
                                                                }
                                                                placeholder={
                                                                    translations[
                                                                        "discount_amount"
                                                                    ] ||
                                                                    "Amount"
                                                                }
                                                            />
                                                        </div>
                                                        <div className="col-4">
                                                            <select
                                                                className="form-select"
                                                                value={
                                                                    discountType
                                                                }
                                                                onChange={
                                                                    this
                                                                        .handleDiscountTypeChange
                                                                }
                                                            >
                                                                <option value="fixed">
                                                                    {translations[
                                                                        "fixed"
                                                                    ] ||
                                                                        "Fixed"}
                                                                </option>
                                                                <option value="percentage">
                                                                    %
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="mb-3">
                                                    <label className="form-label small text-muted mb-1">
                                                        {translations["note"] ||
                                                            "Note"}
                                                    </label>
                                                    <textarea
                                                        className="form-control"
                                                        rows="2"
                                                        value={note}
                                                        onChange={
                                                            this
                                                                .handleNoteChange
                                                        }
                                                        placeholder={
                                                            translations[
                                                                "add_note"
                                                            ] ||
                                                            "Add order note..."
                                                        }
                                                    ></textarea>
                                                </div>

                                                <div className="mb-3">
                                                    <label className="form-label small text-muted mb-1 mr-2">
                                                        {translations[
                                                            "payment_method"
                                                        ] || "Payment Method"}
                                                    </label>
                                                    <select
                                                        className="form-select"
                                                        value={paymentMethod}
                                                        onChange={
                                                            this
                                                                .handlePaymentMethodChange
                                                        }
                                                    >
                                                        <option value="cash">
                                                            {translations[
                                                                "cash"
                                                            ] || "Cash"}
                                                        </option>
                                                        <option value="card">
                                                            {translations[
                                                                "card"
                                                            ] || "Card"}
                                                        </option>
                                                        <option value="bank_transfer">
                                                            {translations[
                                                                "bank_transfer"
                                                            ] ||
                                                                "Bank Transfer"}
                                                        </option>
                                                        <option value="ewallet">
                                                            {translations[
                                                                "ewallet"
                                                            ] || "E-Wallet"}
                                                        </option>
                                                    </select>
                                                </div>

                                                {paymentMethod === "cash" && (
                                                    <div className="mb-3">
                                                        <div className="row g-2">
                                                            <div className="col-6">
                                                                <label className="form-label small text-muted mb-1">
                                                                    {translations[
                                                                        "cash_amount"
                                                                    ] ||
                                                                        "Cash Amount"}
                                                                </label>
                                                                <input
                                                                    type="number"
                                                                    min="0"
                                                                    className="form-control"
                                                                    value={
                                                                        cashAmount
                                                                    }
                                                                    onChange={
                                                                        this
                                                                            .handleCashAmountChange
                                                                    }
                                                                />
                                                            </div>
                                                            <div className="col-6">
                                                                <label className="form-label small text-muted mb-1">
                                                                    {translations[
                                                                        "change"
                                                                    ] ||
                                                                        "Change"}
                                                                </label>
                                                                <input
                                                                    type="text"
                                                                    className="form-control bg-white"
                                                                    value={`${
                                                                        window
                                                                            .APP
                                                                            .currency_symbol
                                                                    } ${changeAmount.toFixed(
                                                                        2
                                                                    )}`}
                                                                    readOnly
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                )}

                                                <div className="bg-white rounded p-3 mb-3">
                                                    <div className="d-flex justify-content-between mb-2">
                                                        <span className="text-muted">
                                                            {translations[
                                                                "subtotal"
                                                            ] || "Subtotal"}
                                                            :
                                                        </span>
                                                        <span>
                                                            {
                                                                window.APP
                                                                    .currency_symbol
                                                            }{" "}
                                                            {parseFloat(
                                                                subtotal
                                                            ).toFixed(2)}
                                                        </span>
                                                    </div>

                                                    {discount > 0 && (
                                                        <div className="d-flex justify-content-between mb-2 text-danger">
                                                            <span className="text-muted">
                                                                {translations[
                                                                    "discount"
                                                                ] || "Discount"}
                                                                :
                                                            </span>
                                                            <span>
                                                                -{" "}
                                                                {
                                                                    window.APP
                                                                        .currency_symbol
                                                                }{" "}
                                                                {(
                                                                    subtotal -
                                                                    total
                                                                ).toFixed(2)}
                                                                {discountType ===
                                                                "percentage"
                                                                    ? ` (${discount}%)`
                                                                    : ""}
                                                            </span>
                                                        </div>
                                                    )}

                                                    <div className="d-flex justify-content-between fw-bold fs-5 border-top pt-2">
                                                        <span>
                                                            {translations[
                                                                "total"
                                                            ] || "Total"}
                                                            :
                                                        </span>
                                                        <span>
                                                            {
                                                                window.APP
                                                                    .currency_symbol
                                                            }{" "}
                                                            {parseFloat(
                                                                total
                                                            ).toFixed(2)}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div className="d-flex gap-2">
                                                    <button
                                                        className="btn btn-outline-danger flex-grow-1"
                                                        onClick={
                                                            this.handleCancel
                                                        }
                                                        disabled={
                                                            cart.length === 0
                                                        }
                                                    >
                                                        <i className="fas fa-times me-2 mr-2"></i>
                                                        {translations[
                                                            "cancel"
                                                        ] || "Cancel"}
                                                    </button>
                                                    <button
                                                        className="btn btn-primary flex-grow-1"
                                                        onClick={
                                                            this.handleCheckout
                                                        }
                                                        disabled={
                                                            cart.length === 0
                                                        }
                                                    >
                                                        <i className="fas fa-check me-2 mr-2"></i>
                                                        {translations[
                                                            "checkout"
                                                        ] || "Checkout"}
                                                    </button>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Products Section */}
                            <div className="col-lg-8">
                                <div className="card">
                                    <div className="card-header bg-white border-bottom">
                                        <div className="d-flex justify-content-between align-items-center">
                                            <h5 className="mb-0 text-dark">
                                                <i
                                                    className="fas fa-boxes me-2 mr-2"
                                                    style={{
                                                        color: "var(--primary)",
                                                    }}
                                                ></i>
                                                {translations["products"] ||
                                                    "Products"}
                                            </h5>
                                            <div className="d-flex gap-2">
                                                <button
                                                    className="btn btn-hold btn-sm mr-2"
                                                    onClick={this.holdOrder}
                                                    disabled={cart.length === 0}
                                                >
                                                    <i className="fas fa-pause me-1"></i>
                                                    {translations[
                                                        "hold_order"
                                                    ] || "Hold"}
                                                </button>
                                                <button
                                                    className="btn btn-retrieve btn-sm"
                                                    onClick={
                                                        this.retrieveHoldOrder
                                                    }
                                                >
                                                    <i className="fas fa-folder-open me-1"></i>
                                                    {translations[
                                                        "retrieve_order"
                                                    ] || "Retrieve"}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="card-body">
                                        <div className="mb-3">
                                            <div className="input-group">
                                                <input
                                                    type="text"
                                                    className="form-control"
                                                    placeholder={`${
                                                        translations[
                                                            "search_product"
                                                        ] || "Search product"
                                                    }...`}
                                                    value={search}
                                                    onChange={
                                                        this.handleChangeSearch
                                                    }
                                                    onKeyDown={this.handleSeach}
                                                />
                                                {search ? (
                                                    <button
                                                        className="btn btn-outline-secondary"
                                                        onClick={() => {
                                                            this.setState({
                                                                search: "",
                                                            });
                                                            this.loadProducts(
                                                                ""
                                                            );
                                                        }}
                                                    >
                                                        <i className="fas fa-times"></i>
                                                    </button>
                                                ) : (
                                                    <button className="btn btn-outline-secondary">
                                                        <i className="fas fa-search"></i>
                                                    </button>
                                                )}
                                            </div>
                                        </div>

                                        <div className="products-grid">
                                            {products.length > 0 ? (
                                                products.map((product) => (
                                                    <div
                                                        key={product.id}
                                                        className="product-card"
                                                        onClick={() =>
                                                            this.addProductToCart(
                                                                product.barcode
                                                            )
                                                        }
                                                        onContextMenu={(e) => {
                                                            e.preventDefault();
                                                            this.showProductDetails(
                                                                product
                                                            );
                                                        }}
                                                    >
                                                        <div className="product-img-container">
                                                            <img
                                                                src={
                                                                    product.image_url ||
                                                                    "/img/no-image.jpg"
                                                                }
                                                                className="product-img"
                                                                alt={
                                                                    product.name
                                                                }
                                                            />
                                                            {product.barcode && (
                                                                <div className="product-barcode">
                                                                    {
                                                                        product.barcode
                                                                    }
                                                                </div>
                                                            )}
                                                            {product.quantity <=
                                                                window.APP
                                                                    .warning_quantity && (
                                                                <span
                                                                    className={`product-stock-badge badge ${
                                                                        product.quantity ===
                                                                        0
                                                                            ? "bg-danger"
                                                                            : "bg-warning"
                                                                    }`}
                                                                >
                                                                    {product.quantity ===
                                                                    0
                                                                        ? translations[
                                                                              "out_of_stock"
                                                                          ] ||
                                                                          "Out Of Stock"
                                                                        : translations[
                                                                              "low_stock"
                                                                          ] ||
                                                                          "Low Stock"}
                                                                </span>
                                                            )}
                                                        </div>
                                                        <div className="product-info">
                                                            <div
                                                                className="product-name"
                                                                title={
                                                                    product.name
                                                                }
                                                            >
                                                                {product.name}
                                                            </div>
                                                            <div className="product-details">
                                                                <span className="product-price">
                                                                    {
                                                                        window
                                                                            .APP
                                                                            .currency_symbol
                                                                    }{" "}
                                                                    {parseFloat(
                                                                        product.price
                                                                    ).toFixed(
                                                                        2
                                                                    )}
                                                                </span>
                                                                <span className="product-stock">
                                                                    {
                                                                        product.quantity
                                                                    }{" "}
                                                                    {translations[
                                                                        "stock"
                                                                    ] ||
                                                                        "Stock"}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))
                                            ) : (
                                                <div className="col-12 text-center py-5 text-muted">
                                                    <i className="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                                    <p>
                                                        {translations[
                                                            "no_products"
                                                        ] ||
                                                            "No products found"}
                                                    </p>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                </div>

                {/* Product Details Modal */}
                {showProductDetails && selectedProduct && (
                    <div className="modal-overlay">
                        <div className="modal-content">
                            <div className="modal-header border-bottom">
                                <h5 className="modal-title">
                                    {translations["product_details"] ||
                                        "Product Details"}
                                </h5>
                                <span
                                    className="close-btn"
                                    onClick={this.closeProductDetails}
                                >
                                    &times;
                                </span>
                            </div>
                            <div className="modal-body">
                                <div className="row">
                                    <div className="col-md-5">
                                        <img
                                            src={
                                                selectedProduct.image_url ||
                                                "/img/no-image.jpg"
                                            }
                                            alt={selectedProduct.name}
                                            className="img-fluid rounded mb-3"
                                        />
                                    </div>
                                    <div className="col-md-7">
                                        <table className="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">
                                                        {translations[
                                                            "product_name"
                                                        ] || "Name"}
                                                    </th>
                                                    <td>
                                                        {selectedProduct.name}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        {translations[
                                                            "barcode"
                                                        ] || "Barcode"}
                                                    </th>
                                                    <td>
                                                        {
                                                            selectedProduct.barcode
                                                        }
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        {translations[
                                                            "price"
                                                        ] || "Price"}
                                                    </th>
                                                    <td>
                                                        {
                                                            window.APP
                                                                .currency_symbol
                                                        }{" "}
                                                        {parseFloat(
                                                            selectedProduct.price
                                                        ).toFixed(2)}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>
                                                        {translations[
                                                            "stock"
                                                        ] || "Stock"}
                                                    </th>
                                                    <td>
                                                        {
                                                            selectedProduct.quantity
                                                        }
                                                        {selectedProduct.quantity <=
                                                            window.APP
                                                                .warning_quantity && (
                                                            <span
                                                                className={`badge ms-2 ${
                                                                    selectedProduct.quantity ===
                                                                    0
                                                                        ? "bg-danger"
                                                                        : "bg-warning"
                                                                }`}
                                                            >
                                                                {selectedProduct.quantity ===
                                                                0
                                                                    ? translations[
                                                                          "out_of_stock"
                                                                      ] ||
                                                                      "Out of Stock"
                                                                    : translations[
                                                                          "low_stock"
                                                                      ] ||
                                                                      "Low Stock"}
                                                            </span>
                                                        )}
                                                    </td>
                                                </tr>
                                                {selectedProduct.category && (
                                                    <tr>
                                                        <th>
                                                            {translations[
                                                                "category"
                                                            ] || "Category"}
                                                        </th>
                                                        <td>
                                                            {
                                                                selectedProduct
                                                                    .category
                                                                    .name
                                                            }
                                                        </td>
                                                    </tr>
                                                )}
                                                <tr>
                                                    <th>
                                                        {translations[
                                                            "description"
                                                        ] || "Description"}
                                                    </th>
                                                    <td>
                                                        {selectedProduct.description ||
                                                            "-"}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div className="modal-footer border-top">
                                <button
                                    className="btn btn-secondary"
                                    onClick={this.closeProductDetails}
                                >
                                    {translations["close"] || "Close"}
                                </button>
                                <button
                                    className="btn btn-primary"
                                    onClick={() => {
                                        this.addProductToCart(
                                            selectedProduct.barcode
                                        );
                                        this.closeProductDetails();
                                    }}
                                    disabled={selectedProduct.quantity <= 0}
                                >
                                    <i className="fas fa-cart-plus me-2"></i>
                                    {translations["add_to_cart"] ||
                                        "Add to Cart"}
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        );
    }

    viewTransactionDetails(transactionId) {
        axios
            .get(`/orders/${transactionId}`)
            .then((res) => {
                const transaction = res.data;

                // Pastikan kalkulasi dilakukan dengan benar
                const subtotal = parseFloat(transaction.subtotal);
                const discount = parseFloat(transaction.discount || 0);
                const total = parseFloat(transaction.total);
                const paid = parseFloat(transaction.paid || 0);
                const change = parseFloat(transaction.change || 0);

                const orderDetails = transaction.order_items
                    .map((item) => {
                        // Pastikan harga dan total tiap item dihitung dengan benar
                        const price = parseFloat(item.price);
                        const itemTotal = (item.quantity * price).toFixed(2);

                        return `<tr>
                                <td>${item.product.name}</td>
                                <td>${item.quantity}</td>
                                <td class="text-end">${
                                    window.APP.currency_symbol
                                } ${price.toFixed(2)}</td>
                                <td class="text-end">${
                                    window.APP.currency_symbol
                                } ${itemTotal}</td>
                            </tr>`;
                    })
                    .join("");

                Swal.fire({
                    title: `Order #${transaction.invoice_number}`,
                    html: `
                        <div class="text-start mb-3">
                            <p><strong>${
                                this.state.translations["date"] || "Date"
                            }:</strong> ${new Date(
                        transaction.created_at
                    ).toLocaleString()}</p>
                            <p><strong>${
                                this.state.translations["customer"] ||
                                "Customer"
                            }:</strong> ${
                        transaction.customer
                            ? `${transaction.customer.first_name} ${transaction.customer.last_name}`
                            : this.state.translations["general_customer"] ||
                              "General Customer"
                    }</p>
                            <p><strong>${
                                this.state.translations["payment_method"] ||
                                "Payment Method"
                            }:</strong> ${transaction.payment_method.toUpperCase()}</p>
                            ${
                                transaction.note
                                    ? `<p><strong>${
                                          this.state.translations["note"] ||
                                          "Note"
                                      }:</strong> ${transaction.note}</p>`
                                    : ""
                            }
                        </div>
                        
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>${
                                        this.state.translations["product"] ||
                                        "Product"
                                    }</th>
                                    <th>${
                                        this.state.translations["quantity"] ||
                                        "Qty"
                                    }</th>
                                    <th class="text-end">${
                                        this.state.translations["price"] ||
                                        "Price"
                                    }</th>
                                    <th class="text-end">${
                                        this.state.translations["total"] ||
                                        "Total"
                                    }</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${orderDetails}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">${
                                        this.state.translations["subtotal"] ||
                                        "Subtotal"
                                    }:</th>
                                    <th class="text-end">${
                                        window.APP.currency_symbol
                                    } ${subtotal.toFixed(2)}</th>
                                </tr>
                                ${
                                    discount > 0
                                        ? `
                                    <tr>
                                        <th colspan="3" class="text-end">${
                                            this.state.translations[
                                                "discount"
                                            ] || "Discount"
                                        }:</th>
                                        <th class="text-end">- ${
                                            window.APP.currency_symbol
                                        } ${discount.toFixed(2)}</th>
                                    </tr>
                                `
                                        : ""
                                }
                                <tr>
                                    <th colspan="3" class="text-end">${
                                        this.state.translations["total"] ||
                                        "Total"
                                    }:</th>
                                    <th class="text-end">${
                                        window.APP.currency_symbol
                                    } ${total.toFixed(2)}</th>
                                </tr>
                                ${
                                    transaction.payment_method === "cash"
                                        ? `
                                    <tr>
                                        <th colspan="3" class="text-end">${
                                            this.state.translations[
                                                "paid_amount"
                                            ] || "Paid Amount"
                                        }:</th>
                                        <th class="text-end">${
                                            window.APP.currency_symbol
                                        } ${paid.toFixed(2)}</th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">${
                                            this.state.translations["change"] ||
                                            "Change"
                                        }:</th>
                                        <th class="text-end">${
                                            window.APP.currency_symbol
                                        } ${change.toFixed(2)}</th>
                                    </tr>
                                `
                                        : ""
                                }
                            </tfoot>
                        </table>
                    `,
                    width: "600px",
                    confirmButtonText:
                        this.state.translations["print_receipt"] ||
                        "Print Receipt",
                    showCancelButton: true,
                    cancelButtonText:
                        this.state.translations["close"] || "Close",
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.printReceipt(transaction);
                    }
                });
            })
            .catch((err) => {
                Swal.fire(
                    "Error",
                    "Failed to load transaction details",
                    "error"
                );
                console.error("Error loading transaction details:", err);
            });
    }
}

export default Cart;

const rootElement = document.getElementById("cart");
if (rootElement) {
    const root = createRoot(rootElement);
    root.render(<Cart />);
}

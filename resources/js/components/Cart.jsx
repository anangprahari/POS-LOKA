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
            paymentMethod: "cash",
            discount: 0,
            discountType: "fixed",
            note: "",
            showTransactionHistory: false,
            recentTransactions: [],
            cashAmount: 0,
            showProductDetails: false,
            selectedProduct: null,
            holdOrders: [],
        };

        // Original bindings
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

        // New method bindings
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
    }

    componentDidMount() {
        this.loadTranslations();
        this.loadCart();
        this.loadProducts();
        this.loadCustomers();
        this.loadRecentTransactions();
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
        axios.get("/cart").then((res) => this.setState({ cart: res.data }));
    }

    handleOnChangeBarcode(e) {
        this.setState({ barcode: e.target.value });
    }

    handleScanBarcode(e) {
        e.preventDefault();
        const { barcode } = this.state;
        if (!barcode) return;
        axios
            .post("/cart", { barcode })
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
                // percentage
                finalTotal = subtotal * (1 - this.state.discount / 100);
            }
        }

        return finalTotal.toFixed(2);
    }

    getSubtotal(cart) {
        return sum(cart.map((c) => c.pivot.quantity * c.price)).toFixed(2);
    }

    handleClickDelete(product_id) {
        axios.delete("/cart/delete", { data: { product_id } }).then(() =>
            this.setState((state) => ({
                cart: state.cart.filter((c) => c.id !== product_id),
            }))
        );
    }

    handleEmptyCart() {
        axios
            .post("/cart/empty", { _method: "DELETE" })
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
        this.setState({ customer_id: e.target.value });
    }

    handleClickSubmit() {
        // Validasi stok dan item di keranjang
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

        // Validasi jumlah uang tunai jika metode pembayaran adalah cash
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

        // Persiapkan data untuk dikirim ke server
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

        // Tampilkan loading
        Swal.fire({
            title: this.state.translations["processing"] || "Processing...",
            text: this.state.translations["please_wait"] || "Please wait...",
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        // Kirim data ke server
        axios
            .post("/orders", orderData)
            .then((response) => {
                Swal.close();

                if (response.data.success) {
                    // Get the order data for printing
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
                        // Reset keranjang dan data terkait
                        this.setState({
                            cart: [],
                            customer_id: null,
                            customer_name:
                                this.state.translations["general_customer"] ||
                                "General Customer",
                            paymentMethod: "cash",
                            discount: 0,
                            discountType: "fixed",
                            note: "",
                            cashAmount: 0,
                            search: "",
                        });

                        // Perbarui data transaksi terbaru
                        this.loadRecentTransactions();

                        // Cetak struk jika user memilih "Print Receipt"
                        if (result.isConfirmed && orderData) {
                            this.printReceipt(orderData);
                        }
                    });
                } else {
                    Swal.fire({
                        title: this.state.translations["error"] || "Error",
                        text:
                            this.state.translations["order_error"] ||
                            "There was an error processing your order",
                        icon: "error",
                    });
                }
            })
            .catch((error) => {
                Swal.close();
                console.error("Error submitting order:", error);

                let errorMessage =
                    this.state.translations["order_error"] ||
                    "There was an error processing your order";

                // Handle ValidationException
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
        return cash >= total ? (cash - total).toFixed(2) : "0.00";
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
        this.handleEmptyCart();
    };

    handleCheckout = () => {
        this.handleClickSubmit();
    };

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
        } = this.state;

        const changeAmount = this.calculateChange();

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
                    .tabs {
                        display: flex;
                        border-bottom: 1px solid #dee2e6;
                        margin-bottom: 15px;
                    }
                    .tab {
                        padding: 8px 16px;
                        cursor: pointer;
                        border: 1px solid transparent;
                        border-top-left-radius: 4px;
                        border-top-right-radius: 4px;
                        margin-bottom: -1px;
                    }
                    .tab.active {
                        color: #495057;
                        background-color: #fff;
                        border-color: #dee2e6 #dee2e6 #fff;
                    }
                    .badge {
                        display: inline-block;
                        padding: 0.25em 0.4em;
                        font-size: 75%;
                        font-weight: 700;
                        line-height: 1;
                        text-align: center;
                        white-space: nowrap;
                        vertical-align: baseline;
                        border-radius: 0.25rem;
                    }
                    .badge-success { background-color: #28a745; color: white; }
                    .badge-warning { background-color: #ffc107; color: black; }
                    .badge-danger { background-color: #dc3545; color: white; }
                    .badge-info { background-color: #17a2b8; color: white; }
                    .modal {
                        display: block;
                        position: fixed;
                        z-index: 1050;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        overflow: auto;
                        background-color: rgba(0,0,0,0.4);
                    }
                    .modal-content {
                        background-color: #fefefe;
                        margin: 10% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                        max-width: 500px;
                        border-radius: 5px;
                    }
                    .close-btn {
                        color: #aaa;
                        float: right;
                        font-size: 28px;
                        font-weight: bold;
                        cursor: pointer;
                    }
                    .strikethrough {
                        text-decoration: line-through;
                        color: #999;
                    }
                    `}
                </style>

                {/* Toggle between normal view and transaction history */}
                <div className="tabs">
                    <div
                        className={`tab ${
                            !showTransactionHistory ? "active" : ""
                        }`}
                        onClick={() =>
                            this.setState({ showTransactionHistory: false })
                        }
                    >
                        {translations["pos"] || "Point of Sale"}
                    </div>
                    <div
                        className={`tab ${
                            showTransactionHistory ? "active" : ""
                        }`}
                        onClick={this.toggleTransactionHistory}
                    >
                        {translations["transactions"] || "Recent Transactions"}
                    </div>
                </div>

                {/* Main Content */}
                {showTransactionHistory ? (
                    // Transaction History View
                    <div className="row">
                        <div className="col-12">
                            <div className="card shadow-sm">
                                <div className="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <h5 className="mb-0">
                                        {translations["recent_transactions"] ||
                                            "Recent Transactions"}
                                    </h5>
                                    <button
                                        className="btn btn-sm btn-light"
                                        onClick={() =>
                                            this.loadRecentTransactions()
                                        }
                                    >
                                        <i className="fas fa-sync-alt"></i>{" "}
                                        {translations["refresh"] || "Refresh"}
                                    </button>
                                </div>
                                <div className="card-body">
                                    <div className="table-responsive">
                                        <table className="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        {translations[
                                                            "invoice_id"
                                                        ] || "Invoice ID"}
                                                    </th>
                                                    <th>
                                                        {translations["date"] ||
                                                            "Date"}
                                                    </th>
                                                    <th>
                                                        {translations[
                                                            "customer"
                                                        ] || "Customer"}
                                                    </th>
                                                    <th>
                                                        {translations[
                                                            "payment_method"
                                                        ] || "Payment Method"}
                                                    </th>
                                                    <th className="text-end">
                                                        {translations[
                                                            "subtotal"
                                                        ] || "Subtotal"}
                                                    </th>
                                                    <th className="text-end">
                                                        {translations[
                                                            "discount"
                                                        ] || "Discount"}
                                                    </th>
                                                    <th className="text-end">
                                                        {translations[
                                                            "total"
                                                        ] || "Total"}
                                                    </th>
                                                    <th>
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
                                                                        className={`badge badge-${
                                                                            transaction.payment_method ===
                                                                            "cash"
                                                                                ? "success"
                                                                                : transaction.payment_method ===
                                                                                  "card"
                                                                                ? "info"
                                                                                : transaction.payment_method ===
                                                                                  "bank_transfer"
                                                                                ? "warning"
                                                                                : "primary" // untuk e-wallet atau metode lainnya
                                                                        }`}
                                                                    >
                                                                        {(() => {
                                                                            // Menampilkan payment method yang digunakan secara dinamis
                                                                            switch (
                                                                                transaction.payment_method
                                                                            ) {
                                                                                case "cash":
                                                                                    return (
                                                                                        translations[
                                                                                            "cash"
                                                                                        ] ||
                                                                                        "CASH"
                                                                                    );
                                                                                case "card":
                                                                                    return (
                                                                                        translations[
                                                                                            "card"
                                                                                        ] ||
                                                                                        "CARD"
                                                                                    );
                                                                                case "bank_transfer":
                                                                                    return (
                                                                                        translations[
                                                                                            "bank_transfer"
                                                                                        ] ||
                                                                                        "BANK TRANSFER"
                                                                                    );
                                                                                case "ewallet":
                                                                                    return (
                                                                                        translations[
                                                                                            "ewallet"
                                                                                        ] ||
                                                                                        "E-WALLET"
                                                                                    );
                                                                                default:
                                                                                    return transaction.payment_method.toUpperCase();
                                                                            }
                                                                        })()}
                                                                    </span>
                                                                </td>
                                                                <td className="text-end">
                                                                    {
                                                                        window
                                                                            .APP
                                                                            .currency_symbol
                                                                    }{" "}
                                                                    {parseFloat(
                                                                        transaction.subtotal
                                                                    ).toFixed(
                                                                        2
                                                                    )}
                                                                </td>
                                                                <td className="text-end text-danger">
                                                                    {(() => {
                                                                        const discountType =
                                                                            transaction.discount_type ||
                                                                            "fixed";
                                                                        // Fix: Use transaction.discount instead of discount
                                                                        const discount =
                                                                            transaction.discount ||
                                                                            0;

                                                                        const discountAmount =
                                                                            discountType ===
                                                                            "percentage"
                                                                                ? parseFloat(
                                                                                      transaction.subtotal
                                                                                  ) *
                                                                                  (discount /
                                                                                      100)
                                                                                : discount;

                                                                        return discountAmount >
                                                                            0
                                                                            ? `- ${
                                                                                  window
                                                                                      .APP
                                                                                      .currency_symbol
                                                                              } ${parseFloat(
                                                                                  discountAmount
                                                                              ).toFixed(
                                                                                  2
                                                                              )}`
                                                                            : `${window.APP.currency_symbol} 0.00`;
                                                                    })()}
                                                                </td>
                                                                <td className="text-end font-weight-bold">
                                                                    {
                                                                        window
                                                                            .APP
                                                                            .currency_symbol
                                                                    }{" "}
                                                                    {(() => {
                                                                        const subtotal =
                                                                            parseFloat(
                                                                                transaction.subtotal
                                                                            );
                                                                        const discount =
                                                                            transaction.discount ||
                                                                            0;
                                                                        const discountType =
                                                                            transaction.discount_type ||
                                                                            "fixed";
                                                                        const discountAmount =
                                                                            discountType ===
                                                                            "percentage"
                                                                                ? subtotal *
                                                                                  (discount /
                                                                                      100)
                                                                                : discount;
                                                                        const totalAfterDiscount =
                                                                            subtotal -
                                                                            discountAmount;
                                                                        return totalAfterDiscount.toFixed(
                                                                            2
                                                                        );
                                                                    })()}
                                                                </td>

                                                                <td>
                                                                    <div className="btn-group btn-group-sm">
                                                                        <button
                                                                            className="btn btn-outline-primary"
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
                                                                            className="btn btn-outline-success"
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
                                                            colSpan="8"
                                                            className="text-center py-3"
                                                        >
                                                            {translations[
                                                                "no_transactions"
                                                            ] ||
                                                                "No transactions found"}
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
                                                translations["scan_barcode"] ||
                                                "Scan Barcode"
                                            }
                                            value={barcode}
                                            onChange={
                                                this.handleOnChangeBarcode
                                            }
                                        />
                                        <button
                                            className="btn btn-success rounded-pill shadow-sm px-4 d-flex align-items-center"
                                            type="submit"
                                        >
                                            <i
                                                className="fas fa-barcode"
                                                style={{ marginRight: "5px" }}
                                            ></i>
                                            {translations["scan"] || "Scan"}
                                        </button>
                                    </form>

                                    {/* Customer Select */}
                                    <select
                                        className="form-select mb-3"
                                        onChange={this.setCustomerId}
                                        value={this.state.customer_id}
                                    >
                                        <option value="">
                                            {translations["general_customer"] ||
                                                "General Customer"}
                                        </option>
                                        {customers.map((cus) => (
                                            <option key={cus.id} value={cus.id}>
                                                {cus.first_name} {cus.last_name}
                                            </option>
                                        ))}
                                    </select>

                                    {/* Hold/Retrieve Order buttons */}
                                    <div className="d-flex gap-2 mb-3">
                                        <button
                                            className="btn btn-sm btn-warning w-50"
                                            onClick={this.holdOrder}
                                            disabled={cart.length === 0}
                                        >
                                            <i className="fas fa-pause me-1"></i>{" "}
                                            {translations["hold_order"] ||
                                                "Hold Order"}
                                        </button>
                                        <button
                                            className="btn btn-sm btn-info w-50"
                                            onClick={this.retrieveHoldOrder}
                                        >
                                            <i className="fas fa-folder-open me-1"></i>{" "}
                                            {translations["retrieve_order"] ||
                                                "Retrieve Order"}
                                        </button>
                                    </div>

                                    {/* Cart Items */}
                                    <div className="flex-grow-1 overflow-auto">
                                        {cart.length ? (
                                            <table className="table table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            {translations[
                                                                "product_name"
                                                            ] || "Product Name"}
                                                        </th>
                                                        <th>
                                                            {translations[
                                                                "quantity"
                                                            ] || "Quantity"}
                                                        </th>
                                                        <th className="text-end">
                                                            {translations[
                                                                "price"
                                                            ] || "Price"}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {cart.map((c) => (
                                                        <tr key={c.id}>
                                                            <td>
                                                                <div>
                                                                    {c.name}
                                                                </div>
                                                                {c.original_price && (
                                                                    <small className="text-success">
                                                                        {c
                                                                            .discount_info
                                                                            .type ===
                                                                        "percentage"
                                                                            ? `${c.discount_info.amount}% off`
                                                                            : `${window.APP.currency_symbol}${c.discount_info.amount} off`}
                                                                    </small>
                                                                )}
                                                            </td>
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
                                                                    onChange={(
                                                                        e
                                                                    ) =>
                                                                        this.handleChangeQty(
                                                                            c.id,
                                                                            e
                                                                                .target
                                                                                .value
                                                                        )
                                                                    }
                                                                />
                                                                <div className="btn-group btn-group-sm">
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
                                                                    <button
                                                                        className="btn btn-sm btn-outline-primary"
                                                                        onClick={() =>
                                                                            this.applyItemDiscount(
                                                                                c.id
                                                                            )
                                                                        }
                                                                        title={
                                                                            translations[
                                                                                "apply_discount"
                                                                            ] ||
                                                                            "Apply Discount"
                                                                        }
                                                                    >
                                                                        <i className="fas fa-percent"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                            <td className="text-end">
                                                                {c.original_price && (
                                                                    <div className="strikethrough">
                                                                        {
                                                                            window
                                                                                .APP
                                                                                .currency_symbol
                                                                        }{" "}
                                                                        {parseFloat(
                                                                            c.original_price
                                                                        ).toFixed(
                                                                            2
                                                                        )}
                                                                    </div>
                                                                )}
                                                                <div>
                                                                    {
                                                                        window
                                                                            .APP
                                                                            .currency_symbol
                                                                    }{" "}
                                                                    {(
                                                                        c.price *
                                                                        c.pivot
                                                                            .quantity
                                                                    ).toFixed(
                                                                        2
                                                                    )}
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        ) : (
                                            <div className="text-center text-muted py-5">
                                                {translations["no_items"] ||
                                                    "No items in cart"}
                                            </div>
                                        )}
                                    </div>

                                    {/* Order Discount */}
                                    {cart.length > 0 && (
                                        <div className="mb-3 mt-2">
                                            <div className="row g-2 align-items-center">
                                                <div className="col-12">
                                                    <label className="form-label mb-1">
                                                        {translations[
                                                            "order_discount"
                                                        ] || "Order Discount"}
                                                    </label>
                                                </div>
                                                <div className="col-8">
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        className="form-control form-control-sm"
                                                        value={discount}
                                                        onChange={
                                                            this
                                                                .handleDiscountChange
                                                        }
                                                        placeholder={
                                                            translations[
                                                                "discount_amount"
                                                            ] ||
                                                            "Discount Amount"
                                                        }
                                                    />
                                                </div>
                                                <div className="col-4">
                                                    <select
                                                        className="form-select form-select-sm"
                                                        value={discountType}
                                                        onChange={
                                                            this
                                                                .handleDiscountTypeChange
                                                        }
                                                    >
                                                        <option value="fixed">
                                                            {translations[
                                                                "fixed"
                                                            ] || "Fixed"}
                                                        </option>
                                                        <option value="percentage">
                                                            %
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Order Note */}
                                    {cart.length > 0 && (
                                        <div className="mb-3">
                                            <label className="form-label mb-1">
                                                {translations["note"] || "Note"}
                                            </label>
                                            <textarea
                                                className="form-control form-control-sm"
                                                rows="2"
                                                value={note}
                                                onChange={this.handleNoteChange}
                                                placeholder={
                                                    translations["add_note"] ||
                                                    "Add note to this order"
                                                }
                                            ></textarea>
                                        </div>
                                    )}

                                    {/* Payment Method */}
                                    {cart.length > 0 && (
                                        <div className="mb-3">
                                            <label className="form-label mb-1">
                                                {translations[
                                                    "payment_method"
                                                ] || "Payment Method"}
                                            </label>
                                            <select
                                                className="form-select form-select-sm"
                                                value={paymentMethod}
                                                onChange={
                                                    this
                                                        .handlePaymentMethodChange
                                                }
                                            >
                                                <option value="cash">
                                                    {translations["cash"] ||
                                                        "Cash"}
                                                </option>
                                                <option value="card">
                                                    {translations["card"] ||
                                                        "Card"}
                                                </option>
                                                <option value="bank_transfer">
                                                    {translations[
                                                        "bank_transfer"
                                                    ] || "Bank Transfer"}
                                                </option>
                                                <option value="ewallet">
                                                    {translations["ewallet"] ||
                                                        "E-Wallet"}
                                                </option>
                                            </select>
                                        </div>
                                    )}

                                    {/* Cash Payment - Change Calculation */}
                                    {cart.length > 0 &&
                                        paymentMethod === "cash" && (
                                            <div className="mb-3">
                                                <div className="row g-2 align-items-center">
                                                    <div className="col-6">
                                                        <label className="form-label mb-1">
                                                            {translations[
                                                                "cash_amount"
                                                            ] || "Cash Amount"}
                                                        </label>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            className="form-control form-control-sm"
                                                            value={cashAmount}
                                                            onChange={
                                                                this
                                                                    .handleCashAmountChange
                                                            }
                                                        />
                                                    </div>
                                                    <div className="col-6">
                                                        <label className="form-label mb-1">
                                                            {translations[
                                                                "change"
                                                            ] || "Change"}
                                                        </label>
                                                        <input
                                                            type="text"
                                                            className="form-control form-control-sm"
                                                            value={`${window.APP.currency_symbol} ${changeAmount}`}
                                                            readOnly
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        )}

                                    {/* Total & Buttons */}
                                    <div className="mt-auto pt-3 border-top">
                                        <div className="d-flex justify-content-between mb-1">
                                            <span>
                                                {translations["subtotal"] ||
                                                    "Subtotal"}
                                                :
                                            </span>
                                            <span>
                                                {window.APP.currency_symbol}{" "}
                                                {this.getSubtotal(cart)}
                                            </span>
                                        </div>

                                        {discount > 0 && (
                                            <div className="d-flex justify-content-between mb-1 text-danger">
                                                <span>
                                                    {translations["discount"] ||
                                                        "Discount"}
                                                    :
                                                </span>
                                                <span>
                                                    -{" "}
                                                    {window.APP.currency_symbol}{" "}
                                                    {(
                                                        this.getSubtotal(cart) -
                                                        this.getTotal(cart)
                                                    ).toFixed(2)}{" "}
                                                    {discountType ===
                                                    "percentage"
                                                        ? `(${discount}%)`
                                                        : ""}
                                                </span>
                                            </div>
                                        )}

                                        <div className="d-flex justify-content-between mb-3">
                                            <strong>
                                                {translations["total"] ||
                                                    "Total"}
                                                :
                                            </strong>
                                            <strong>
                                                {window.APP.currency_symbol}{" "}
                                                {this.getTotal(cart)}
                                            </strong>
                                        </div>

                                        <div className="d-flex justify-content-between gap-2">
                                            <button
                                                type="button"
                                                className="btn btn-danger rounded-pill px-4 shadow-sm w-50"
                                                onClick={this.handleCancel}
                                                disabled={cart.length === 0}
                                            >
                                                <i
                                                    className="fas fa-times me-2"
                                                    style={{
                                                        marginRight: "8px",
                                                    }}
                                                ></i>
                                                {translations["cancel"] ||
                                                    "Cancel"}
                                            </button>
                                            <button
                                                type="button"
                                                className="btn btn-primary rounded-pill px-4 shadow-sm w-50"
                                                onClick={this.handleCheckout}
                                                disabled={cart.length === 0}
                                            >
                                                <i
                                                    className="fas fa-shopping-cart me-2"
                                                    style={{
                                                        marginRight: "8px",
                                                    }}
                                                ></i>
                                                {translations["checkout"] ||
                                                    "Checkout"}
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
                                    placeholder={`${
                                        translations["search_product"] ||
                                        "Search product"
                                    }...`}
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
                                        {translations["reset"] || "Reset"}
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
                                            className="card h-100 shadow-sm cursor-pointer position-relative"
                                            onClick={() =>
                                                this.addProductToCart(p.barcode)
                                            }
                                            onContextMenu={(e) => {
                                                e.preventDefault();
                                                this.showProductDetails(p);
                                            }}
                                        >
                                            {/* Stock Badge */}
                                            {p.quantity <=
                                                window.APP.warning_quantity && (
                                                <div className="position-absolute top-0 end-0 m-2">
                                                    <span
                                                        className={`badge ${
                                                            p.quantity === 0
                                                                ? "badge-danger"
                                                                : "badge-warning"
                                                        }`}
                                                    >
                                                        {p.quantity === 0
                                                            ? translations[
                                                                  "out_of_stock"
                                                              ] ||
                                                              "Out of Stock"
                                                            : translations[
                                                                  "low_stock"
                                                              ] || "Low Stock"}
                                                    </span>
                                                </div>
                                            )}

                                            <img
                                                src={
                                                    p.image_url ||
                                                    "/img/no-image.jpg"
                                                }
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
                                                <div className="d-flex justify-content-between align-items-center mt-2">
                                                    <small className="text-muted">
                                                        {translations[
                                                            "stock"
                                                        ] || "Stock"}
                                                        : {p.quantity}
                                                    </small>
                                                    <strong className="text-primary">
                                                        {
                                                            window.APP
                                                                .currency_symbol
                                                        }{" "}
                                                        {parseFloat(
                                                            p.price
                                                        ).toFixed(2)}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                )}

                {/* Product Details Modal */}
                {showProductDetails && selectedProduct && (
                    <div className="modal">
                        <div className="modal-content">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <h5 className="mb-0">
                                    {translations["product_details"] ||
                                        "Product Details"}
                                </h5>
                                <span
                                    className="close-btn"
                                    onClick={() => this.closeProductDetails()}
                                >
                                    &times;
                                </span>
                            </div>

                            <div className="row">
                                <div className="col-md-4">
                                    <img
                                        src={
                                            selectedProduct.image_url ||
                                            "/img/no-image.jpg"
                                        }
                                        alt={selectedProduct.name}
                                        className="img-fluid rounded mb-3"
                                    />
                                </div>
                                <div className="col-md-8">
                                    <table className="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th>
                                                    {translations[
                                                        "product_name"
                                                    ] || "Product Name"}
                                                </th>
                                                <td>{selectedProduct.name}</td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    {translations["barcode"] ||
                                                        "Barcode"}
                                                </th>
                                                <td>
                                                    {selectedProduct.barcode}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    {translations["price"] ||
                                                        "Price"}
                                                </th>
                                                <td>
                                                    {window.APP.currency_symbol}{" "}
                                                    {parseFloat(
                                                        selectedProduct.price
                                                    ).toFixed(2)}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>
                                                    {translations["stock"] ||
                                                        "Stock"}
                                                </th>
                                                <td>
                                                    {selectedProduct.quantity}
                                                    {selectedProduct.quantity <=
                                                        window.APP
                                                            .warning_quantity && (
                                                        <span
                                                            className={`badge ms-2 ${
                                                                selectedProduct.quantity ===
                                                                0
                                                                    ? "badge-danger"
                                                                    : "badge-warning"
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
                                                                .category.name
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

                                    <div className="d-flex gap-2 mt-3">
                                        <button
                                            className="btn btn-primary w-50"
                                            onClick={() => {
                                                this.addProductToCart(
                                                    selectedProduct.barcode
                                                );
                                                this.closeProductDetails();
                                            }}
                                            disabled={
                                                selectedProduct.quantity <= 0
                                            }
                                        >
                                            <i className="fas fa-cart-plus me-2"></i>
                                            {translations["add_to_cart"] ||
                                                "Add to Cart"}
                                        </button>
                                        <button
                                            className="btn btn-outline-secondary w-50"
                                            onClick={() =>
                                                this.closeProductDetails()
                                            }
                                        >
                                            {translations["close"] || "Close"}
                                        </button>
                                    </div>
                                </div>
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

const root = document.getElementById("cart");
if (root) {
    const rootInstance = createRoot(root);
    rootInstance.render(<Cart />);
}

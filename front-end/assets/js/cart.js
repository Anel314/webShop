let cart = [];

let user = null;

let isAppInitialized = false;

function decodeJwt(token) {
  try {
    const base64Url = token.split(".")[1];
    const base64 = base64Url.replace(/-/g, "+").replace(/_/g, "/");
    const decodedPayload = JSON.parse(window.atob(base64));
    return decodedPayload;
  } catch (e) {
    console.error("Error decoding JWT:", e);
    return null;
  }
}

let globalRenderCartItems = () => console.warn("Cart renderer not ready.");

async function fetchCartDataAndRender() {
  if (!isAppInitialized) {
    console.warn("App not initialized. Skipping cart re-fetch.");
    return;
  }

  try {
    const response = await fetch(
      `http://localhost/webShop/back-end/cart/${user.user.id}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          auth: `Bearer ${sessionStorage.getItem("auth")}`,
        },
      }
    );
    if (response.ok) {
      cart = await response.json();
      globalRenderCartItems();
    } else {
      console.error("Failed to re-fetch cart after update.");
    }
  } catch (e) {
    console.error("Network error during cart re-fetch:", e);
  }
}

async function addToCart(productId, buttonElement) {
  if (!isAppInitialized) {
    alert("You must log in before adding items to the cart.");
    return;
  }

  if (!user || !user.user || !user.user.id) {
    console.error("User ID is missing or invalid.");
    alert("Authentication failed. Cannot add item.");
    return;
  }

  buttonElement.disabled = true;
  buttonElement.textContent = "Adding...";

  const payload = {
    user_id: user.user.id,
    product_id: productId,
    quantity: 1,
  };

  try {
    const apiResponse = await fetch(`http://localhost/webShop/back-end/cart`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        auth: `Bearer ${sessionStorage.getItem("auth")}`,
      },
      body: JSON.stringify(payload),
    });

    if (!apiResponse.ok) {
      const errorText = await apiResponse.text();
      console.error("API Error Response:", errorText);
      throw new Error(
        `API failed to add product to cart. Status: ${apiResponse.status}`
      );
    }

    alert(`Product added successfully!`);
    await fetchCartDataAndRender();
  } catch (error) {
    console.error("Error adding product to cart:", error);
    alert(
      "Failed to add product to cart. Please check the console for details."
    );
  } finally {
    buttonElement.disabled = false;
    buttonElement.textContent = "Add to Cart";
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  user = decodeJwt(sessionStorage.getItem("auth"));

  if (!sessionStorage.getItem("auth")) {
    document.getElementById("cart-icon-button").style.display = "none";
    return;
  } else {
    document.getElementById("cart-icon-button").style.display = "block";
  }

  try {
    const response = await fetch(
      `http://localhost/webShop/back-end/cart/${user.user.id}`,
      {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          auth: `Bearer ${sessionStorage.getItem("auth")}`,
        },
      }
    );
    cart = await response.json();
  } catch (error) {
    console.error("Initial cart fetch failed:", error);
    cart = [];
  }

  let isModalOpen = false;

  const cartIconButton = document.getElementById("cart-icon-button");
  const cartModal = document.getElementById("cart-modal");
  const cartProductList = document.getElementById("cart-product-list");
  const itemCountBadge = document.getElementById("item-count-badge");
  const cartTotalElement = document.getElementById("cart-total-price");
  const cartSummary = document.querySelector(".cart-summary");
  const emptyMessage = document.getElementById("empty-cart-message");
  const orderButton = document.getElementById("order-button");
  const closeButton = document.querySelector(".close-button");

  function updateCartSummary() {
    let totalItems = 0;
    let totalPrice = 0;

    cart.forEach((item) => {
      totalItems += item.quantity;
      totalPrice += item.price * item.quantity;
    });

    cartTotalElement.textContent = `$${totalPrice.toFixed(2)}`;

    if (totalItems > 0) {
      itemCountBadge.textContent = totalItems;
      itemCountBadge.classList.remove("hidden");
      cartSummary.classList.remove("hidden");
      emptyMessage.classList.add("hidden");
    } else {
      itemCountBadge.classList.add("hidden");
      cartSummary.classList.add("hidden");
      emptyMessage.classList.remove("hidden");
    }
  }

  function renderCartItems() {
    cartProductList.innerHTML = "";

    cart.forEach((item) => {
      const li = document.createElement("li");
      li.className = "cart-item";
      const itemSubtotal = item.price * item.quantity;

      li.innerHTML = `
                <div class="item-details">
                    <span class="product-name">${item.name}</span>
                    <span class="product-price">$${itemSubtotal.toFixed(
                      2
                    )}</span>
                </div>
                <div class="item-controls">
                    <button class="quantity-decrease" data-id="${
                      item.product_id
                    }" ${item.quantity <= 1 ? "disabled" : ""}>-</button>
                    <input type="number" value="${
                      item.quantity
                    }" min="1" class="quantity-input" data-id="${
        item.product_id
      }">
                    <button class="quantity-increase" data-id="${
                      item.product_id
                    }">+</button>
                    <button class="remove-button" data-id="${item.product_id}">
                        &times; Remove
                    </button>
                </div>
            `;
      cartProductList.appendChild(li);
    });
    updateCartSummary();
  }

  globalRenderCartItems = renderCartItems;

  function toggleModal() {
    isModalOpen = !isModalOpen;
    if (isModalOpen) {
      cartModal.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    } else {
      cartModal.classList.add("hidden");
      document.body.style.overflow = "";
    }
    cartIconButton.setAttribute("aria-expanded", isModalOpen);
  }

  cartIconButton.addEventListener("click", toggleModal);
  closeButton.addEventListener("click", toggleModal);
  cartModal.addEventListener("click", (event) => {
    if (event.target === cartModal) {
      toggleModal();
    }
  });

  cartProductList.addEventListener("click", async (event) => {
    const target = event.target;
    const id = parseInt(target.getAttribute("data-id"));
    const targetButton = event.target.closest(
      ".remove-button, .quantity-increase, .quantity-decrease"
    );
    if (!targetButton) return;

    const itemIndex = cart.findIndex((i) => parseInt(i.product_id) === id);
    if (itemIndex === -1) return;

    let newQuantity = cart[itemIndex].quantity;

    if (targetButton.classList.contains("remove-button")) {
      newQuantity = 0;
    } else if (targetButton.classList.contains("quantity-increase")) {
      newQuantity += 1;
    } else if (targetButton.classList.contains("quantity-decrease")) {
      if (newQuantity > 1) {
        newQuantity -= 1;
      } else {
        return;
      }
    }

    const payload = {
      user_id: user.user.id,
      product_id: id,
      quantity: newQuantity,
    };

    try {
      const apiResponse = await fetch(
        `http://localhost/webShop/back-end/cart`,
        {
          method: "PUT",
          headers: {
            "Content-Type": "application/json",
            auth: `Bearer ${sessionStorage.getItem("auth")}`,
          },
          body: JSON.stringify(payload),
        }
      );

      if (!apiResponse.ok) {
        throw new Error(`API update failed with status: ${apiResponse.status}`);
      }

      if (newQuantity === 0) {
        cart.splice(itemIndex, 1);
      } else {
        cart[itemIndex].quantity = newQuantity;
      }
      globalRenderCartItems();
    } catch (error) {
      console.error("Failed to update cart quantity via API:", error);
      alert("Failed to update cart. Please try again.");
    }
  });

  cartProductList.addEventListener("change", async (event) => {
    const target = event.target;
    if (target.classList.contains("quantity-input")) {
      const id = parseInt(target.getAttribute("data-id"));
      let newQuantity = parseInt(target.value);
      const itemIndex = cart.findIndex((i) => parseInt(i.product_id) === id);

      if (itemIndex === -1) return;

      if (newQuantity < 1 || isNaN(newQuantity)) {
        newQuantity = 1;
        target.value = 1;
      }

      const payload = {
        user_id: user.user.id,
        product_id: id,
        quantity: newQuantity,
      };

      try {
        const apiResponse = await fetch(
          `http://localhost/webShop/back-end/cart`,
          {
            method: "PUT",
            headers: {
              "Content-Type": "application/json",
              auth: `Bearer ${sessionStorage.getItem("auth")}`,
            },
            body: JSON.stringify(payload),
          }
        );

        if (!apiResponse.ok) {
          throw new Error(
            `API update failed with status: ${apiResponse.status}`
          );
        }

        cart[itemIndex].quantity = newQuantity;
        globalRenderCartItems();
      } catch (error) {
        console.error("Failed to update cart quantity via API:", error);
        alert("Failed to update cart. Please try again.");
        target.value = cart[itemIndex].quantity;
      }
    }
  });

  orderButton.addEventListener("click", () => {
    if (cart.length > 0) {
      const data = {
        user_id: cart[0].cart_user_id,
        cart_id: cart[0].cart_id,
        shipping_address: user.user.address,
      };
      try {
        fetch("http://localhost/webShop/back-end/orders", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            auth: `Bearer ${sessionStorage.getItem("auth")}`,
          },
          body: JSON.stringify(data),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error(
                `API order failed with status: ${response.status}`
              );
            }
            return response.json();
          })
          .then((orderResponse) => {
            alert("Order placed successfully!");
            cart = [];
            globalRenderCartItems();
            toggleModal();
          })
          .catch((error) => {
            console.error("Failed to place order via API:", error);
            alert("Failed to place order. Please try again.");
          });
      } catch (error) {
        console.error("Failed to place order via API:", error);
        alert("Failed to place order. Please try again.");
      }
    } else {
      alert("Your cart is empty!");
    }
  });

  globalRenderCartItems();
  isAppInitialized = true;
});

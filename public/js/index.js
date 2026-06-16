const categories = [
  "Mouse",
  "Keyboard",
  "Audio",
  "Collectibles",
  "Merchandise",
];

function loadContent() {
  categories.forEach((category) => {
    const categoryList = document.createElement("div");
    categoryList.className = "category-list";

    const categoryName = document.createElement("h1");
    categoryName.classList = "category-name";
    categoryName.innerHTML = `${category}`;

    const itemList = document.createElement("div");
    itemList.className = "item-list";
    itemList.id = `${category.toLowerCase()}-list`;

    categoryList.appendChild(categoryName);
    categoryList.appendChild(itemList);

    document.body.appendChild(categoryList);
  });

  loadProducts();
}

async function loadProducts() {
  const response = await fetch(
    "http://localhost/gaming-store-webapp/public/api/fetch_products.php",
  );
  const result = await response.json();

  for (const data of result.data) {
    const itemList = document.querySelector(
      `#${data.category.toLowerCase()}-list`,
    );

    itemList.innerHTML += `
    <div class="item">
        <div class="item-img-container">
            <img
                src=".${data.image_path}"
                alt=""
                class="item-img"
            />
        </div>
        <h3 class="item-name">${data.name}</h3>
        <p class="item-price">RM${data.price}</p>
    </div>
    `;
  }
}

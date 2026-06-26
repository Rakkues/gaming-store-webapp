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
  const response = await fetch("/gaming-store-webapp/src/api/api_products.php");
  const result = await response.json();

  console.log(result);

  for (const data of result) {
    const itemList = document.querySelector(
      `#${data.category.toLowerCase()}-list`,
    );

    if (data.stock === 0) {
      itemList.innerHTML += `
        <div class="item">
            <div class="item-img-container">
                <img
                    src=".${data.image_path}"
                    alt=""
                    class="item-img"
                />
            </div>
            <div class="item-description">
              <a href="pages/shopping/product.php?id=${data.id}"><h3 class="item-name">${data.name}</h3></a>
              <p class="item-price">SOLD OUT</p>
            </div>
        </div>
        `;
    } else {
      itemList.innerHTML += `
        <div class="item">
            <div class="item-img-container">
                <img
                    src=".${data.image_path}"
                    alt=""
                    class="item-img"
                />
            </div>
            <div class="item-description">
              <a href="pages/shopping/product.php?id=${data.id}"><h3 class="item-name">${data.name}</h3></a>
              <p class="item-price">RM${data.price}</p>
            </div>
        </div>
        `;
    }
  }
}

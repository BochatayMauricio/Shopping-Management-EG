const carousel = document.getElementById("carousel");
const prevBtn = document.querySelector(".carousel-btn.prev");
const nextBtn = document.querySelector(".carousel-btn.next");
const searchForm = document.getElementById("searchForm");
const searchResults = document.getElementById("searchResults");
const promotions = [
  { local: "Tienda A", discount: 30, description: "Descuento especial en productos de verano", image: "https://via.placeholder.com/400x200" },
  { local: "Tienda B", discount: 20, description: "Ofertas flash solo por hoy", image: "https://via.placeholder.com/400x200" },
  { local: "Tienda C", discount: 50, description: "Hasta 50% off en marcas seleccionadas", image: "https://via.placeholder.com/400x200" },
  { local: "Tienda D", discount: 15, description: "Promoción limitada", image: "https://via.placeholder.com/400x200" },
];
const cardWidth = 1000; // ancho de tarjeta + margen (ajustar si cambias el gap)

  prevBtn.addEventListener("click", () => {
    console.log('Previous button clicked');
    carousel.scrollBy({ left: -cardWidth, behavior: "smooth" });
  });

  nextBtn.addEventListener("click", () => {
    console.log('Previous button clicked');
    carousel.scrollBy({ left: cardWidth, behavior: "smooth" });
  });

// Función para mostrar promociones
function displayPromotions(list) {
  searchResults.innerHTML = "";
  if (list.length === 0) {
    searchResults.innerHTML = "<p>No se encontraron promociones.</p>";
    return;
  }

  list.forEach(promo => {
    const card = document.createElement("div");
    card.className = "carousel-card";
    card.innerHTML = `
      <img src="${promo.image}" alt="${promo.local}">
      <h3>${promo.local}</h3>
      <p>${promo.description}</p>
      <p><strong>${promo.discount}% OFF</strong></p>
    `;
    searchResults.appendChild(card);
  });
}

// Mostrar promociones por defecto al cargar la página
document.addEventListener("DOMContentLoaded", () => {
  displayPromotions(promotions); // muestra todas por defecto
});

// Manejar búsqueda
searchForm.addEventListener("submit", (e) => {
  e.preventDefault();

  const localValue = document.getElementById("localName").value.toLowerCase();
  const discountValue = parseInt(document.getElementById("discount").value);

  // Filtrado
  const filtered = promotions.filter(promo => {
    const matchesLocal = promo.local.toLowerCase().includes(localValue);
    const matchesDiscount = isNaN(discountValue) ? true : promo.discount >= discountValue;
    return matchesLocal && matchesDiscount;
  });

  // Mostrar resultados filtrados
  displayPromotions(filtered);
});
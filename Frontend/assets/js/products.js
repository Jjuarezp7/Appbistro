// ============================
// 🔹 PRODUCTOS
// ============================
if (document.getElementById("products-table"))
{
    fetch("../Backend/api/products.php")
        .then(res => res.json())
        .then(data =>
        {
            const tbody = document.getElementById("products-table");
            tbody.innerHTML = "";
            if (!data.length)
            {
                tbody.innerHTML = "<tr><td colspan='6'>Sin productos registrados</td></tr>";
                return;
            }
            data.forEach(prod =>
            {
                tbody.innerHTML += `
          <tr>
            <td>${prod.id}</td>
            <td>${prod.nombre}</td>
            <td>Q ${parseFloat(prod.precio).toFixed(2)}</td>
            <td>${prod.categoria || ""}</td>
            <td>${prod.receta_nombre || "-"}</td>
            <td>
              <button class="btn btn-sm btn-warning" 
                onclick="editProduct(${prod.id}, '${prod.nombre}', ${prod.precio}, ${prod.categoria_id}, ${prod.receta_id || 'null'})">Editar</button>
              <button class="btn btn-sm btn-danger" onclick="deleteProduct(${prod.id})">Eliminar</button>
            </td>
          </tr>
        `;
            });
        })
        .catch(err => console.error("Error cargando productos:", err));
}

// ============================
// 🔹 Guardar producto
// ============================
const productForm = document.getElementById("productForm");
if (productForm)
{
    productForm.addEventListener("submit", e =>
    {
        e.preventDefault();

        const id = document.getElementById("productId").value;
        const nombre = document.getElementById("productNombre").value;
        const precio = document.getElementById("productPrecio").value;
        const categoria_id = document.getElementById("productCategoria").value;
        const receta_id = document.getElementById("productReceta").value || null;

        const url = id ? "../Backend/api/products_edit.php" : "../Backend/api/products_add.php";

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id, nombre, precio, categoria_id, receta_id })
        })
            .then(res => res.json())
            .then(data =>
            {
                if (data.error)
                {
                    alert("Error: " + data.error);
                } else
                {
                    location.reload();
                }
            })
            .catch(err => console.error("Error guardando producto:", err));
    });
}

// ============================
// 🔹 Editar producto
// ============================
function editProduct(id, nombre, precio, categoria_id, receta_id)
{
    document.getElementById("productId").value = id;
    document.getElementById("productNombre").value = nombre;
    document.getElementById("productPrecio").value = precio;
    document.getElementById("productCategoria").value = categoria_id;

    // Seleccionar receta asociada
    const recetaSelect = document.getElementById("productReceta");
    recetaSelect.value = receta_id || "";

    // Mostrar ingredientes de la receta seleccionada
    mostrarIngredientesReceta(receta_id);

    new bootstrap.Modal(document.getElementById("productModal")).show();
}

// ============================
// 🔹 Eliminar producto
// ============================
function deleteProduct(id)
{
    if (confirm("¿Eliminar este producto?"))
    {
        fetch("../Backend/api/products_delete.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        })
            .then(res => res.json())
            .then(data =>
            {
                if (data.error)
                {
                    alert("Error: " + data.error);
                } else
                {
                    location.reload();
                }
            })
            .catch(err => console.error("Error eliminando producto:", err));
    }
}

// ============================
// 🔹 Mostrar ingredientes de receta
// ============================
async function mostrarIngredientesReceta(recetaId)
{
    const detalle = document.getElementById("recetaDetalle");
    if (!recetaId)
    {
        detalle.innerHTML = "<em>Sin receta asociada</em>";
        return;
    }
    try
    {
        const res = await fetch(`../Backend/api/recipes_get.php?id=${recetaId}`);
        const receta = await res.json();
        if (receta.ingredientes && receta.ingredientes.length)
        {
            let html = "<h6>Ingredientes:</h6><ul>";
            receta.ingredientes.forEach(i =>
            {
                html += `<li>${i.nombre}: ${i.cantidad} ${i.unidad}</li>`;
            });
            html += "</ul>";
            detalle.innerHTML = html;
        } else
        {
            detalle.innerHTML = "<em>Esta receta no tiene ingredientes</em>";
        }
    } catch (err)
    {
        console.error("Error cargando ingredientes de receta:", err);
        detalle.innerHTML = "<em>Error cargando ingredientes</em>";
    }
}

// Vincular evento al combo de recetas
document.getElementById("productReceta")?.addEventListener("change", (e) =>
{
    mostrarIngredientesReceta(e.target.value);
});
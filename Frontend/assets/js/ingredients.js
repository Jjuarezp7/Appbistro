// =======================
//  INGREDIENTES CRUD
// =======================

const ingredientsTable = document.getElementById("ingredients-table");

if (ingredientsTable)
{
    // 🔹 Cargar lista de ingredientes
    fetch("../Backend/api/ingredients.php")
        .then(res =>
        {
            if (!res.ok) throw new Error("HTTP " + res.status);
            return res.json();
        })
        .then(data =>
        {
            ingredientsTable.innerHTML = "";
            data.forEach(ing =>
            {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${ing.id}</td>
                    <td>${ing.nombre}</td>
                    <td>${ing.categoria || ""}</td>
                    <td>${ing.cantidad}</td>
                    <td>${ing.unidad || ""}</td>
                    <td>
                        <button class="btn btn-sm btn-warning"
                            data-id="${ing.id}"
                            data-nombre="${ing.nombre}"
                            data-categoria_id="${ing.categoria_id}"
                            data-cantidad="${ing.cantidad}"
                            data-unidad_id="${ing.unidad_id}">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-sm btn-danger" data-id="${ing.id}">
                            🗑️ Eliminar
                        </button>
                    </td>
                `;
                ingredientsTable.appendChild(tr);
            });
        })
        .catch(err => console.error("Error cargando ingredientes:", err));

    // 🔹 Manejo de clicks en la tabla
    ingredientsTable.addEventListener("click", e =>
    {
        const btn = e.target.closest("button");
        if (!btn) return;

        // Editar
        if (btn.classList.contains("btn-warning"))
        {
            document.getElementById("ingredientId").value = btn.dataset.id;
            document.getElementById("ingNombre").value = btn.dataset.nombre;
            document.getElementById("ingCategoria").value = btn.dataset.categoria_id; // selecciona la categoría
            document.getElementById("ingCantidad").value = btn.dataset.cantidad;
            document.getElementById("ingUnidad").value = btn.dataset.unidad_id; // selecciona la unidad
            new bootstrap.Modal(document.getElementById("ingredientModal")).show();
        }

        // Eliminar
        if (btn.classList.contains("btn-danger"))
        {
            const id = btn.dataset.id;
            if (confirm("¿Eliminar este ingrediente?"))
            {
                fetch("../Backend/api/ingredients_delete.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id })
                })
                    .then(res =>
                    {
                        if (!res.ok) throw new Error("HTTP " + res.status);
                        return res.json();
                    })
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
                    .catch(err => console.error("Error eliminando ingrediente:", err));
            }
        }
    });
}

// =======================
//  FORMULARIO CREAR/EDITAR
// =======================

const ingredientForm = document.getElementById("ingredientForm");

if (ingredientForm)
{
    ingredientForm.addEventListener("submit", e =>
    {
        e.preventDefault();

        const id = document.getElementById("ingredientId").value;
        const nombre = document.getElementById("ingNombre").value;
        const categoria_id = document.getElementById("ingCategoria").value;
        const cantidad = document.getElementById("ingCantidad").value;
        const unidad_id = document.getElementById("ingUnidad").value;

        const url = id
            ? "../Backend/api/ingredients_edit.php"
            : "../Backend/api/ingredients_add.php";

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id, nombre, categoria_id, cantidad, unidad_id })
        })
            .then(res =>
            {
                if (!res.ok) throw new Error("HTTP " + res.status);
                return res.json();
            })
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
            .catch(err => console.error("Error guardando ingrediente:", err));
    });
}

// =======================
//  LISTA SIMPLE (para selects en recetas)
// =======================

function cargarIngredientesSimple(selectId)
{
    fetch("../Backend/api/ingredients_list_simple.php")
        .then(res =>
        {
            if (!res.ok) throw new Error("HTTP " + res.status);
            return res.json();
        })
        .then(data =>
        {
            const select = document.getElementById(selectId);
            if (select)
            {
                select.innerHTML = "";
                data.forEach(ing =>
                {
                    const opt = document.createElement("option");
                    opt.value = ing.id;
                    opt.textContent = ing.nombre;
                    select.appendChild(opt);
                });
            }
        })
        .catch(err => console.error("Error cargando lista simple de ingredientes:", err));
}
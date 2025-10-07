// ============================
// 📖 RECETAS: listado, CRUD y manejo de ingredientes
// ============================
const API_BASE = "../Backend/api/recipes";
const API_INGREDIENTS = "../Backend/api/ingredients_list_simple.php";

let ingredientesCatalogo = []; // {id, nombre, unidad}
let recetas = [];
let modalReceta;

// Helpers
function getUnidadIngrediente(id)
{
    const ing = ingredientesCatalogo.find(i => i.id === Number(id));
    return ing ? ing.unidad : "-";
}
function getNombreIngrediente(id)
{
    const ing = ingredientesCatalogo.find(i => i.id === Number(id));
    return ing ? ing.nombre : "";
}

// Inicialización
document.addEventListener("DOMContentLoaded", () =>
{
    if (!document.getElementById("tablaRecetas")) return;

    modalReceta = new bootstrap.Modal(document.getElementById("modalReceta"));

    cargarCatalogoIngredientes().then(() =>
    {
        cargarRecetas();
    });

    document.getElementById("btnNuevaReceta").addEventListener("click", nuevaReceta);
    document.getElementById("buscarReceta").addEventListener("input", filtrarRecetas);
    document.getElementById("btnAgregarIngrediente").addEventListener("click", () =>
    {
        agregarFilaIngrediente();
    });
    document.getElementById("formReceta").addEventListener("submit", guardarReceta);
});

// ============================
// 🔹 Cargar catálogo de ingredientes
// ============================
async function cargarCatalogoIngredientes()
{
    const res = await fetch(API_INGREDIENTS);
    ingredientesCatalogo = await res.json(); // [{id,nombre,unidad}]
}

// ============================
// 🔹 Cargar recetas
// ============================
async function cargarRecetas()
{
    const res = await fetch(`${API_BASE}/list.php`);
    recetas = await res.json();
    renderRecetas(recetas);
}

function renderRecetas(list)
{
    const tbody = document.getElementById("tablaRecetas");
    tbody.innerHTML = "";
    if (!list.length)
    {
        tbody.innerHTML = "<tr><td colspan='5'>Sin recetas registradas</td></tr>";
        return;
    }

    list.forEach(r =>
    {
        const ingredientesText = r.ingredientes?.map(i => `${i.nombre} (${i.cantidad} ${i.unidad})`).join(", ") || "-";
        tbody.innerHTML += `
          <tr>
            <td>${r.nombre}</td>
            <td>${r.descripcion || ""}</td>
            <td>${r.tipo || "-"}</td>
            <td>${ingredientesText}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary me-2" onclick="editarReceta(${r.id})">Editar</button>
              <button class="btn btn-sm btn-outline-danger" onclick="eliminarReceta(${r.id})">Eliminar</button>
            </td>
          </tr>
        `;
    });
}

function filtrarRecetas(e)
{
    const q = e.target.value.toLowerCase();
    renderRecetas(recetas.filter(r => r.nombre.toLowerCase().includes(q)));
}

// ============================
// 🔹 Nueva receta
// ============================
function nuevaReceta()
{
    document.getElementById("modalRecetaTitulo").textContent = "Nueva receta";
    document.getElementById("recetaId").value = "";
    document.getElementById("recetaNombre").value = "";
    document.getElementById("recetaDescripcion").value = "";
    document.getElementById("recetaTipo").value = "ingrediente"; // default
    document.getElementById("tablaRecetaIngredientes").innerHTML = "";
    agregarFilaIngrediente(); // primera fila por defecto
    modalReceta.show();
}

// ============================
// 🔹 Editar receta
// ============================
async function editarReceta(id)
{
    const res = await fetch(`${API_BASE}/get.php?id=${id}`);
    const r = await res.json();

    document.getElementById("modalRecetaTitulo").textContent = "Editar receta";
    document.getElementById("recetaId").value = r.id;
    document.getElementById("recetaNombre").value = r.nombre;
    document.getElementById("recetaDescripcion").value = r.descripcion || "";
    document.getElementById("recetaTipo").value = r.tipo || "ingrediente";

    const tbody = document.getElementById("tablaRecetaIngredientes");
    tbody.innerHTML = "";

    if (r.ingredientes && r.ingredientes.length)
    {
        r.ingredientes.forEach(ing =>
        {
            agregarFilaIngrediente(ing.ingredient_id, ing.cantidad);
        });
    } else
    {
        agregarFilaIngrediente();
    }

    modalReceta.show();
}

// ============================
// 🔹 Agregar fila ingrediente
// ============================
function agregarFilaIngrediente(ingredientId = "", cantidad = "")
{
    const tbody = document.getElementById("tablaRecetaIngredientes");
    const rowId = `row_${Date.now()}_${Math.floor(Math.random() * 1000)}`;

    const opciones = ingredientesCatalogo.map(i => `<option value="${i.id}">${i.nombre}</option>`).join("");

    const tr = document.createElement("tr");
    tr.id = rowId;
    tr.innerHTML = `
        <td>
          <select class="form-select form-select-sm ingrediente-select" required>
            <option value="">Seleccione</option>
            ${opciones}
          </select>
        </td>
        <td class="unidad-cell text-muted">-</td>
        <td>
          <input type="number" class="form-control form-control-sm cantidad-input" min="0" step="0.01" placeholder="0" required>
        </td>
        <td class="text-end">
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFila('${rowId}')">Eliminar</button>
        </td>
    `;
    tbody.appendChild(tr);

    const select = tr.querySelector(".ingrediente-select");
    const unidadCell = tr.querySelector(".unidad-cell");
    const cantidadInput = tr.querySelector(".cantidad-input");

    select.addEventListener("change", () =>
    {
        unidadCell.textContent = getUnidadIngrediente(select.value) || "-";
    });

    // Set inicial si se pasa argumento
    if (ingredientId)
    {
        select.value = ingredientId;
        unidadCell.textContent = getUnidadIngrediente(ingredientId) || "-";
    }
    if (cantidad !== "") cantidadInput.value = cantidad;
}

function eliminarFila(rowId)
{
    const tr = document.getElementById(rowId);
    if (tr) tr.remove();
}

// ============================
// 🔹 Guardar receta (add/edit)
// ============================
async function guardarReceta(e)
{
    e.preventDefault();

    const id = document.getElementById("recetaId").value;
    const nombre = document.getElementById("recetaNombre").value.trim();
    const descripcion = document.getElementById("recetaDescripcion").value.trim();
    const tipo = document.getElementById("recetaTipo").value;

    // Obtener ingredientes de la tabla
    const filas = Array.from(document.querySelectorAll("#tablaRecetaIngredientes tr"));
    const ingredientes = filas.map(tr =>
    {
        const select = tr.querySelector(".ingrediente-select");
        const cantidadInput = tr.querySelector(".cantidad-input");
        return {
            ingredient_id: Number(select.value),
            cantidad: Number(cantidadInput.value)
        };
    }).filter(i => i.ingredient_id && i.cantidad > 0);

    // Validaciones
    if (!nombre) return alert("El nombre es obligatorio");
    if (!ingredientes.length) return alert("Agrega al menos un ingrediente con cantidad > 0");

    // Prevenir duplicados
    const ids = ingredientes.map(i => i.ingredient_id);
    const tieneDuplicados = ids.some((v, i) => ids.indexOf(v) !== i);
    if (tieneDuplicados) return alert("No repitas ingredientes. Ajusta cantidades en una sola fila.");

    const payload = id
        ? { id: Number(id), nombre, descripcion, tipo, ingredientes }
        : { nombre, descripcion, tipo, ingredientes };

    const url = id ? `${API_BASE}/edit.php` : `${API_BASE}/add.php`;
    const res = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (data.status === "ok")
    {
        modalReceta.hide();
        await cargarRecetas();
    } else
    {
        alert(data.error || "Error al guardar receta");
    }
}

// ============================
// 🔹 Eliminar receta
// ============================
async function eliminarReceta(id)
{
    if (!confirm("¿Eliminar esta receta? Esta acción no se puede deshacer.")) return;

    const res = await fetch(`${API_BASE}/delete.php?id=${id}`, { method: "GET" });
    const data = await res.json();

    if (data.status === "ok")
    {
        await cargarRecetas();
    } else
    {
        alert(data.error || "Error al eliminar receta");
    }
}
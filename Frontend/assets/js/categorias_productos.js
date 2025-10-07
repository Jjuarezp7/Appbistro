document.addEventListener("DOMContentLoaded", () =>
{
    const tbody = document.getElementById("categorias-table");
    const categoriaModal = new bootstrap.Modal(document.getElementById("categoriaModal"));
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));

    const categoriaForm = document.getElementById("categoriaForm");
    const categoriaId = document.getElementById("categoriaId");
    const catNombre = document.getElementById("catNombre");
    const categoriaModalTitle = document.getElementById("categoriaModalTitle");

    const deleteCategoriaId = document.getElementById("deleteCategoriaId");
    const deleteText = document.getElementById("deleteText");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    // Endpoints backend
    const API_LIST = "../Backend/api/categorias_productos_list.php";
    const API_ADD = "../Backend/api/categorias_productos_add.php";
    const API_EDIT = "../Backend/api/categorias_productos_edit.php";
    const API_DELETE = "../Backend/api/categorias_productos_delete.php";

    // Cargar lista
    function cargarCategorias()
    {
        tbody.innerHTML = '<tr><td colspan="3">Cargando...</td></tr>';
        fetch(API_LIST)
            .then(res => res.json())
            .then(data =>
            {
                tbody.innerHTML = "";
                if (!data || data.length === 0)
                {
                    tbody.innerHTML = '<tr><td colspan="3">Sin categorías registradas</td></tr>';
                    return;
                }
                data.forEach(c =>
                {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${c.id}</td>
                        <td>${c.nombre}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-2" data-action="edit" data-id="${c.id}" data-nombre="${c.nombre}">Editar</button>
                            <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${c.id}" data-nombre="${c.nombre}">Eliminar</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err =>
            {
                console.error("Error cargando categorías:", err);
                tbody.innerHTML = '<tr><td colspan="3">Error al cargar datos</td></tr>';
            });
    }

    // Abrir modal para nueva categoría
    document.querySelector("[data-bs-target='#categoriaModal']").addEventListener("click", () =>
    {
        categoriaModalTitle.textContent = "Nueva Categoría";
        categoriaId.value = "";
        catNombre.value = "";
    });

    // Click en acciones (editar / eliminar)
    tbody.addEventListener("click", (e) =>
    {
        const btn = e.target.closest("button");
        if (!btn) return;
        const action = btn.getAttribute("data-action");
        const id = btn.getAttribute("data-id");

        if (action === "edit")
        {
            categoriaModalTitle.textContent = "Editar Categoría";
            categoriaId.value = id;
            catNombre.value = btn.getAttribute("data-nombre");
            categoriaModal.show();
        }

        if (action === "delete")
        {
            deleteCategoriaId.value = id;
            deleteText.textContent = `¿Seguro que deseas eliminar la categoría "${btn.getAttribute("data-nombre")}" (ID ${id})?`;
            confirmDeleteModal.show();
        }
    });

    // Guardar (crear/editar)
    categoriaForm.addEventListener("submit", (e) =>
    {
        e.preventDefault();
        const payload = {
            id: parseInt(categoriaId.value || "0"),
            nombre: catNombre.value.trim()
        };
        if (!payload.nombre)
        {
            alert("El nombre es obligatorio.");
            return;
        }

        const isEdit = payload.id > 0;
        const url = isEdit ? API_EDIT : API_ADD;

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
            .then(res => res.json())
            .then(json =>
            {
                if (json.error)
                {
                    alert(json.error);
                    return;
                }
                categoriaModal.hide();
                cargarCategorias();
            })
            .catch(err =>
            {
                console.error("Error guardando categoría:", err);
                alert("Error al guardar categoría");
            });
    });

    // Confirmar eliminar
    confirmDeleteBtn.addEventListener("click", () =>
    {
        const id = parseInt(deleteCategoriaId.value || "0");
        if (!id) return;

        fetch(API_DELETE, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        })
            .then(res => res.json())
            .then(json =>
            {
                if (json.error)
                {
                    alert(json.error);
                    return;
                }
                confirmDeleteModal.hide();
                cargarCategorias();
            })
            .catch(err =>
            {
                console.error("Error eliminando categoría:", err);
                alert("Error al eliminar categoría");
            });
    });

    // Inicializar
    cargarCategorias();
});
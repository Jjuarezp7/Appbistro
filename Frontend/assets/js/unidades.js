document.addEventListener("DOMContentLoaded", () =>
{
    const tbody = document.getElementById("unidades-table");
    const unidadModal = new bootstrap.Modal(document.getElementById("unidadModal"));
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));

    const unidadForm = document.getElementById("unidadForm");
    const unidadId = document.getElementById("unidadId");
    const uniNombre = document.getElementById("uniNombre");
    const uniAbrev = document.getElementById("uniAbrev");
    const unidadModalTitle = document.getElementById("unidadModalTitle");

    const deleteUnidadId = document.getElementById("deleteUnidadId");
    const deleteText = document.getElementById("deleteText");
    const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    // Endpoints backend
    const API_LIST = "../Backend/api/unidades_list.php";
    const API_ADD = "../Backend/api/unidades_add.php";
    const API_EDIT = "../Backend/api/unidades_edit.php";
    const API_DELETE = "../Backend/api/unidades_delete.php";

    // Cargar lista
    function cargarUnidades()
    {
        tbody.innerHTML = '<tr><td colspan="4">Cargando...</td></tr>';
        fetch(API_LIST)
            .then(res => res.json())
            .then(data =>
            {
                tbody.innerHTML = "";
                if (!data || data.length === 0)
                {
                    tbody.innerHTML = '<tr><td colspan="4">Sin unidades registradas</td></tr>';
                    return;
                }
                data.forEach(u =>
                {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${u.id}</td>
                        <td>${u.nombre}</td>
                        <td>${u.abreviatura || "—"}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-2" data-action="edit" data-id="${u.id}" data-nombre="${u.nombre}" data-abrev="${u.abreviatura || ''}">Editar</button>
                            <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${u.id}" data-nombre="${u.nombre}">Eliminar</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            })
            .catch(err =>
            {
                console.error("Error cargando unidades:", err);
                tbody.innerHTML = '<tr><td colspan="4">Error al cargar datos</td></tr>';
            });
    }

    // Abrir modal para nueva unidad
    document.querySelector("[data-bs-target='#unidadModal']").addEventListener("click", () =>
    {
        unidadModalTitle.textContent = "Nueva Unidad";
        unidadId.value = "";
        uniNombre.value = "";
        uniAbrev.value = "";
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
            unidadModalTitle.textContent = "Editar Unidad";
            unidadId.value = id;
            uniNombre.value = btn.getAttribute("data-nombre");
            uniAbrev.value = btn.getAttribute("data-abrev");
            unidadModal.show();
        }

        if (action === "delete")
        {
            deleteUnidadId.value = id;
            deleteText.textContent = `¿Seguro que deseas eliminar la unidad "${btn.getAttribute("data-nombre")}" (ID ${id})?`;
            confirmDeleteModal.show();
        }
    });

    // Guardar (crear/editar)
    unidadForm.addEventListener("submit", (e) =>
    {
        e.preventDefault();
        const payload = {
            id: parseInt(unidadId.value || "0"),
            nombre: uniNombre.value.trim(),
            abreviatura: uniAbrev.value.trim()
        };
        if (!payload.nombre || !payload.abreviatura)
        {
            alert("Nombre y abreviatura son obligatorios.");
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
                unidadModal.hide();
                cargarUnidades();
            })
            .catch(err =>
            {
                console.error("Error guardando unidad:", err);
                alert("Error al guardar unidad");
            });
    });

    // Confirmar eliminar
    confirmDeleteBtn.addEventListener("click", () =>
    {
        const id = parseInt(deleteUnidadId.value || "0");
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
                cargarUnidades();
            })
            .catch(err =>
            {
                console.error("Error eliminando unidad:", err);
                alert("Error al eliminar unidad");
            });
    });

    // Inicializar
    cargarUnidades();
});
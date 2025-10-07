document.addEventListener("DOMContentLoaded", () =>
{
    const bitacoraTable = document.getElementById("bitacora-table");
    const paginationDiv = document.getElementById("pagination");
    const exportBtn = document.getElementById("exportAllBtn");
    const exportPageBtn = document.getElementById("exportPageBtn");
    if (!bitacoraTable) return;

    // Leer filtros desde la URL
    const params = new URLSearchParams(window.location.search);
    const usuario = params.get("usuario") || "";
    const accion = params.get("accion") || "";
    const fecha = params.get("fecha") || "";
    let page = parseInt(params.get("page") || "1");
    const limit = 20;

    function cargarBitacora(pagina = 1)
    {
        let url = `../Backend/api/bitacora.php?mode=json&page=${pagina}&limit=${limit}`;
        if (usuario) url += `&usuario=${encodeURIComponent(usuario)}`;
        if (accion) url += `&accion=${encodeURIComponent(accion)}`;
        if (fecha) url += `&fecha=${encodeURIComponent(fecha)}`;

        fetch(url)
            .then(res => res.json())
            .then(json =>
            {
                const data = json.data;
                bitacoraTable.innerHTML = "";

                if (data.length === 0)
                {
                    bitacoraTable.innerHTML = `<tr><td colspan="5">No hay registros en la bitácora</td></tr>`;
                    return;
                }

                data.forEach(row =>
                {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${row.id}</td>
                        <td>${row.usuario || "—"}</td>
                        <td>${row.accion}</td>
                        <td>${row.descripcion}</td>
                        <td>${row.fecha}</td>
                    `;
                    bitacoraTable.appendChild(tr);
                });

                renderPagination(json.page, json.pages);
                actualizarBotonesExport(json.page, json.limit);
            })
            .catch(err =>
            {
                console.error("Error cargando bitácora:", err);
                bitacoraTable.innerHTML = `<tr><td colspan="5">Error al cargar datos</td></tr>`;
            });
    }

    function renderPagination(current, totalPages)
    {
        if (!paginationDiv) return;
        paginationDiv.innerHTML = "";

        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++)
        {
            const btn = document.createElement("button");
            btn.textContent = i;
            btn.className = "btn btn-sm " + (i === current ? "btn-primary" : "btn-outline-primary");
            btn.addEventListener("click", () =>
            {
                page = i;
                cargarBitacora(page);
            });
            paginationDiv.appendChild(btn);
        }
    }

    function actualizarBotonesExport(currentPage, limit)
    {
        // Construir base URL con filtros
        let baseUrl = "../Backend/api/bitacora.php?mode=csv";
        if (usuario) baseUrl += `&usuario=${encodeURIComponent(usuario)}`;
        if (accion) baseUrl += `&accion=${encodeURIComponent(accion)}`;
        if (fecha) baseUrl += `&fecha=${encodeURIComponent(fecha)}`;

        // Exportar todo
        if (exportBtn)
        {
            exportBtn.href = baseUrl;
        }

        // Exportar página actual
        if (exportPageBtn)
        {
            exportPageBtn.href = baseUrl + `&page=${currentPage}&limit=${limit}`;
        }
    }

    // Inicializar
    cargarBitacora(page);
});
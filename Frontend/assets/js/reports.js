// ============================
// 🔹 REPORTES
// ============================
if (document.getElementById("ventasChart"))
{
    let ventasChart; // variable global para guardar la instancia del gráfico

    function cargarVentas(inicio = "", fin = "")
    {
        let url = "../Backend/api/reports_sales.php";
        if (inicio && fin)
        {
            url += `?inicio=${inicio}&fin=${fin}`;
        }

        fetch(url)
            .then(res =>
            {
                if (!res.ok) throw new Error("Error HTTP " + res.status);
                return res.json();
            })
            .then(data =>
            {
                const ctx = document.getElementById("ventasChart").getContext("2d");

                // Destruir gráfico previo si existe
                if (ventasChart) ventasChart.destroy();

                // Crear nuevo gráfico
                ventasChart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: data.map(d => d.fecha),
                        datasets: [{
                            label: "Ventas (Q)",
                            data: data.map(d => d.total),
                            borderColor: "rgba(75, 192, 192, 1)",
                            backgroundColor: "rgba(75, 192, 192, 0.2)",
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: true } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            })
            .catch(err => console.error("Error cargando ventas:", err));
    }

    // Inicializar gráfico y filtro
    if (document.getElementById("ventasChart"))
    {
        // Cargar por defecto (últimos 7 días)
        cargarVentas();

        // Manejar filtro de fechas
        const filtroForm = document.getElementById("filtroVentas");
        if (filtroForm)
        {
            filtroForm.addEventListener("submit", e =>
            {
                e.preventDefault();
                const inicio = document.getElementById("ventasInicio").value;
                const fin = document.getElementById("ventasFin").value;
                if (inicio && fin)
                {
                    cargarVentas(inicio, fin);
                } else
                {
                    alert("Selecciona ambas fechas para filtrar");
                }
            });
        }
    }

    // ============================
    // 🔹 REPORTE: Productos más vendidos con filtro
    // ============================
    let productosChart;

    function cargarProductos(inicio = "", fin = "")
    {
        let url = "../Backend/api/reports_top_products.php";
        if (inicio && fin)
        {
            url += `?inicio=${inicio}&fin=${fin}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data =>
            {
                const ctx = document.getElementById("productosChart").getContext("2d");
                if (productosChart) productosChart.destroy();

                productosChart = new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: data.map(d => d.nombre),
                        datasets: [{
                            label: "Cantidad vendida",
                            data: data.map(d => d.cantidad),
                            backgroundColor: "orange"
                        }]
                    }
                });
            });
    }

    if (document.getElementById("productosChart"))
    {
        cargarProductos();

        document.getElementById("filtroProductos").addEventListener("submit", e =>
        {
            e.preventDefault();
            const inicio = document.getElementById("productosInicio").value;
            const fin = document.getElementById("productosFin").value;
            if (inicio && fin)
            {
                cargarProductos(inicio, fin);
            } else
            {
                alert("Selecciona ambas fechas para filtrar");
            }
        });
    }

    // Inventario crítico
    fetch("../Backend/api/reports_critical_inventory.php")
        .then(res => res.json())
        .then(data =>
        {
            const tbody = document.getElementById("critical-inventory");
            tbody.innerHTML = "";
            if (data.length === 0)
            {
                tbody.innerHTML = "<tr><td colspan='3'>Todo el inventario está en buen estado ✅</td></tr>";
            } else
            {
                data.forEach(ing =>
                {
                    tbody.innerHTML += `
            <tr>
              <td>${ing.nombre}</td>
              <td>${ing.cantidad}</td>
              <td>${ing.unidad}</td>
            </tr>
          `;
                });
            }
        });
}

// ============================
// 🔹 REPORTE: Consumo de ingredientes con filtro de fechas
// ============================
let ingredientesChart;

function cargarConsumoIngredientes(inicio = "", fin = "")
{
    let url = "../Backend/api/reports_ingredients_consumption.php";
    if (inicio && fin)
    {
        url += `?inicio=${inicio}&fin=${fin}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data =>
        {
            const ctx = document.getElementById("ingredientesChart").getContext("2d");

            // Si ya existe el gráfico, destruirlo antes de crear uno nuevo
            if (ingredientesChart)
            {
                ingredientesChart.destroy();
            }

            ingredientesChart = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: data.map(d => d.ingrediente),
                    datasets: [{
                        label: "Consumo total",
                        data: data.map(d => d.total_usado),
                        backgroundColor: "green"
                    }]
                }
            });
        });
}

// Cargar por defecto (sin filtro)
if (document.getElementById("ingredientesChart"))
{
    cargarConsumoIngredientes();

    // Manejar formulario de filtro
    const form = document.getElementById("filtroIngredientes");
    form.addEventListener("submit", e =>
    {
        e.preventDefault();
        const inicio = document.getElementById("fechaInicio").value;
        const fin = document.getElementById("fechaFin").value;
        if (inicio && fin)
        {
            cargarConsumoIngredientes(inicio, fin);
        } else
        {
            alert("Selecciona ambas fechas para filtrar");
        }
    });
}
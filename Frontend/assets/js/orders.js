// ============================
// 📦 ÓRDENES
// ============================

let orderProducts = [];

if (document.getElementById("orders-table"))
{
    // Lista de órdenes
    fetch("../Backend/api/orders.php")
        .then(res => res.json())
        .then(data =>
        {
            const tbody = document.getElementById("orders-table");
            tbody.innerHTML = "";
            if (!data.length)
            {
                tbody.innerHTML = "<tr><td colspan='6'>Sin órdenes registradas</td></tr>";
                return;
            }
            data.forEach(order =>
            {
                tbody.innerHTML += `
          <tr>
            <td>${order.id}</td>
            <td>${order.cliente}</td>
            <td>${order.productos || ""}</td>
            <td>Q ${parseFloat(order.total).toFixed(2)}</td>
            <td>${order.estado}</td>
            <td>
              <button class="btn btn-sm btn-info" onclick="verDetalle(${order.id})">👁️ Ver</button>
              <button class="btn btn-sm btn-warning" onclick="editOrder(${order.id})">Editar</button>
              <button class="btn btn-sm btn-danger" onclick="deleteOrder(${order.id})">Eliminar</button>
            </td>
          </tr>
        `;
            });
        });

    // Cargar productos en el select del modal
    fetch("../Backend/api/products.php")
        .then(res => res.json())
        .then(data =>
        {
            const select = document.getElementById("orderProducto");
            if (!select) return;
            select.innerHTML = "";
            data.forEach(prod =>
            {
                select.innerHTML += `<option value="${prod.id}">${prod.nombre}</option>`;
            });
        });
}

// ============================
// 🔹 Agregar producto a la orden temporal
// ============================
function addProductToOrder()
{
    const prodId = document.getElementById("orderProducto").value;
    const prodName = document.getElementById("orderProducto").selectedOptions[0].text;
    const cantidad = document.getElementById("orderCantidad").value;

    if (!cantidad || cantidad <= 0) return alert("Cantidad inválida");

    orderProducts.push({ producto_id: prodId, nombre: prodName, cantidad });
    renderOrderProducts();
}

function renderOrderProducts()
{
    const tbody = document.getElementById("orderProductsTable");
    tbody.innerHTML = "";
    orderProducts.forEach((p, i) =>
    {
        tbody.innerHTML += `
      <tr>
        <td>${p.nombre}</td>
        <td>${p.cantidad}</td>
        <td><button class="btn btn-sm btn-danger" onclick="removeProductFromOrder(${i})">❌</button></td>
      </tr>
    `;
    });
}

function removeProductFromOrder(index)
{
    orderProducts.splice(index, 1);
    renderOrderProducts();
}

// ============================
// 🔹 Guardar orden (crear o editar)
// ============================
const orderForm = document.getElementById("orderForm");
if (orderForm)
{
    orderForm.addEventListener("submit", e =>
    {
        e.preventDefault();

        const id = document.getElementById("orderId").value;
        const cliente = document.getElementById("orderCliente").value;
        const estado = document.getElementById("orderEstado").value;

        if (orderProducts.length === 0)
        {
            alert("Agrega al menos un producto a la orden");
            return;
        }

        const url = id ? "../Backend/api/orders_edit.php" : "../Backend/api/orders_add.php";

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id, cliente, estado, productos: orderProducts })
        })
            .then(res => res.json())
            .then(data =>
            {
                if (data.error) alert("Error: " + data.error);
                else location.reload();
            });
    });
}

// ============================
// 🔹 Editar orden
// ============================
function editOrder(id)
{
    fetch("../Backend/api/orders_detail.php?id=" + id)
        .then(res => res.json())
        .then(data =>
        {
            document.getElementById("orderId").value = data.id;
            document.getElementById("orderCliente").value = data.cliente;
            document.getElementById("orderEstado").value = data.estado;

            orderProducts = data.productos.map(p => ({
                producto_id: p.producto_id,
                nombre: p.nombre,
                cantidad: p.cantidad
            }));
            renderOrderProducts();

            new bootstrap.Modal(document.getElementById("orderModal")).show();
        });
}

// ============================
// 🔹 Eliminar orden
// ============================
function deleteOrder(id)
{
    if (confirm("¿Eliminar esta orden?"))
    {
        fetch("../Backend/api/orders_delete.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        })
            .then(res => res.json())
            .then(data =>
            {
                if (data.error) alert("Error: " + data.error);
                else location.reload();
            });
    }
}

// ============================
// 🔹 Ver detalle de orden (con ingredientes)
// ============================
function verDetalle(id)
{
    fetch("../Backend/api/orders_detail.php?id=" + id)
        .then(res => res.json())
        .then(data =>
        {
            let html = `
              <p><strong>Cliente:</strong> ${data.cliente}</p>
              <p><strong>Total:</strong> Q ${parseFloat(data.total).toFixed(2)}</p>
              <p><strong>Estado:</strong> ${data.estado}</p>
              <h6>Productos:</h6>
              <ul>
            `;
            data.productos.forEach(p =>
            {
                html += `<li>${p.nombre} x${p.cantidad}`;
                if (p.ingredientes && p.ingredientes.length)
                {
                    html += `<ul>`;
                    p.ingredientes.forEach(i =>
                    {
                        html += `<li>${i.nombre}: ${i.cantidad_usada} ${i.unidad}</li>`;
                    });
                    html += `</ul>`;
                }
                html += `</li>`;
            });
            html += `</ul>`;

            document.getElementById("orderDetailBody").innerHTML = html;
            new bootstrap.Modal(document.getElementById("orderDetailModal")).show();
        });
}
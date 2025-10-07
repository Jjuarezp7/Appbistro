
//  Cargar lista de usuarios
function cargarUsuarios()
{
    fetch("../Backend/api/users.php")
        .then(res => res.json())
        .then(data =>
        {
            const tbody = document.getElementById("users-table");
            tbody.innerHTML = "";

            if (data.length === 0)
            {
                tbody.innerHTML = `<tr><td colspan="5">No hay usuarios registrados</td></tr>`;
                return;
            }

            data.forEach(user =>
            {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${user.id}</td>
                    <td>${user.nombre}</td>
                    <td>${user.email}</td>
                    <td>${user.rol}</td>
                    <td>
                        <button class="btn btn-sm btn-warning">✏️ Editar</button>
                        <button class="btn btn-sm btn-danger">🗑️ Eliminar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => console.error("Error cargando usuarios:", err));
}

//  Crear o editar usuario

const userForm = document.getElementById("userForm");
if (userForm)
{
    userForm.addEventListener("submit", e =>
    {
        e.preventDefault();

        const id = document.getElementById("userId").value;
        const payload = {
            id: id || undefined,
            nombre: document.getElementById("userNombre").value,
            email: document.getElementById("userEmail").value,
            rol: document.getElementById("userRol").value
        };

        let url = "../Backend/api/users_add.php";
        if (id)
        {
            url = "../Backend/api/users_edit.php";
            const password = document.getElementById("userPassword").value;
            if (password.trim() !== "")
            {
                payload.password = password; // solo si escribió algo
            }
        } else
        {
            payload.password = document.getElementById("userPassword").value; // en creación siempre
        }

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
            credentials: "include"
        })
            .then(res => res.json())
            .then(data =>
            {
                if (data.status === "ok")
                {
                    alert(id ? "✅ Usuario actualizado" : "✅ Usuario creado");
                    userForm.reset();
                    document.getElementById("userPassword").parentElement.style.display = "block";
                    const modal = bootstrap.Modal.getInstance(document.getElementById("userModal"));
                    modal.hide();
                    cargarUsuarios();
                } else
                {
                    alert("❌ Error: " + (data.error || "No se pudo guardar"));
                }
            })
            .catch(err => console.error("Error guardando usuario:", err));
    });
}

//  Editar usuario (en modal)

document.getElementById("users-table").addEventListener("click", e =>
{
    if (e.target.closest(".btn-warning"))
    {
        const tr = e.target.closest("tr");
        const id = tr.children[0].textContent;
        const nombre = tr.children[1].textContent;
        const email = tr.children[2].textContent;
        const rol = tr.children[3].textContent;

        document.getElementById("userId").value = id;
        document.getElementById("userNombre").value = nombre;
        document.getElementById("userEmail").value = email;
        document.getElementById("userRol").value = rol;

        // Ocultar contraseña en edición y quitar required
        const passInput = document.getElementById("userPassword");
        passInput.parentElement.style.display = "none";
        passInput.removeAttribute("required");

        document.querySelector("#userModal .modal-title").textContent = "Editar usuario";
        const modal = new bootstrap.Modal(document.getElementById("userModal"));
        modal.show();
    }
});


//Eliminar usuario
document.getElementById("users-table").addEventListener("click", e =>
{
    if (e.target.closest(".btn-danger"))
    {
        const tr = e.target.closest("tr");
        const id = tr.children[0].textContent;
        const nombre = tr.children[1].textContent;

        if (confirm(`¿Seguro que deseas eliminar al usuario "${nombre}" (ID: ${id})?`))
        {
            fetch("../Backend/api/users_delete.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: id }),
                credentials: "include"
            })
                .then(res => res.json())
                .then(data =>
                {
                    if (data.status === "ok")
                    {
                        alert("✅ Usuario eliminado correctamente");
                        cargarUsuarios();
                    } else
                    {
                        alert("❌ Error: " + (data.error || "No se pudo eliminar"));
                    }
                })
                .catch(err => console.error("Error eliminando usuario:", err));
        }
    }
});

// Inicializar tabla
if (document.getElementById("users-table"))
{
    cargarUsuarios();
}
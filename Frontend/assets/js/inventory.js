// INVENTARIO
document.addEventListener("DOMContentLoaded", () =>
{
    const table = document.getElementById("inventory-table");
    const adjustForm = document.getElementById("adjustForm");
    const selectIngredient = document.getElementById("adjustIngredient");

    // --- Cargar inventario ---
    if (table)
    {
        fetch("../Backend/api/inventory.php")
            .then(res => res.json())
            .then(data =>
            {
                const tbody = table;
                tbody.innerHTML = "";

                if (!data || data.length === 0)
                {
                    tbody.innerHTML = `<tr><td colspan="6">No hay registros en inventario</td></tr>`;
                    return;
                }

                data.forEach(ing =>
                {
                    tbody.innerHTML += `
                        <tr>
                            <td>${ing.id}</td>
                            <td>${ing.nombre}</td>
                            <td>${ing.categoria || "—"}</td>
                            <td>${ing.cantidad}</td>
                            <td>${ing.unidad || "—"}</td>
                            <td>${ing.actualizado_en || "—"}</td>
                        </tr>
                    `;
                });
            })
            .catch(err =>
            {
                console.error("Error cargando inventario:", err);
                table.innerHTML = `<tr><td colspan="6">Error al cargar inventario</td></tr>`;
            });
    }

    // --- Cargar ingredientes en el select del modal ---
    if (selectIngredient)
    {
        fetch("../Backend/api/ingredients.php")
            .then(res => res.json())
            .then(data =>
            {
                selectIngredient.innerHTML = `<option value="">Selecciona un ingrediente</option>`;
                data.forEach(ing =>
                {
                    selectIngredient.innerHTML += `<option value="${ing.id}">${ing.nombre} (${ing.categoria || "Sin categoría"})</option>`;
                });
            })
            .catch(err =>
            {
                console.error("Error cargando ingredientes:", err);
                selectIngredient.innerHTML = `<option value="">Error al cargar</option>`;
            });
    }

    // --- Guardar ajuste ---
    if (adjustForm)
    {
        adjustForm.addEventListener("submit", e =>
        {
            e.preventDefault();

            const ingredient_id = selectIngredient.value;
            const cantidad = parseFloat(document.getElementById("adjustCantidad").value);
            const tipo = document.getElementById("adjustTipo").value;

            if (!ingredient_id || isNaN(cantidad) || !tipo)
            {
                alert("Completa todos los campos antes de guardar.");
                return;
            }

            fetch("../Backend/api/inventory_adjust.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ ingredient_id, cantidad, tipo })
            })
                .then(res => res.json())
                .then(data =>
                {
                    if (data.error)
                    {
                        alert("Error: " + data.error);
                    } else
                    {
                        // Refrescar tabla sin recargar toda la página
                        alert("Ajuste guardado correctamente");
                        location.reload();
                    }
                })
                .catch(err =>
                {
                    console.error("Error guardando ajuste:", err);
                    alert("Error al guardar ajuste");
                });
        });
    }
});
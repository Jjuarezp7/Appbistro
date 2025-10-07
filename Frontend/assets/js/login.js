const loginForm = document.getElementById("formLogin");
if (loginForm)
{
    loginForm.addEventListener("submit", async (e) =>
    {
        e.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        try
        {
            const res = await fetch("/apbistro2/Backend/api/login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password })
            });

            let data = {};
            try
            {
                data = await res.json(); // intentar parsear JSON
            } catch (parseErr)
            {
                console.error("Respuesta no-JSON del servidor:", parseErr);
            }

            console.log("Respuesta login:", data);

            if (res.ok && data.status === "ok")
            {
                // ✅ Login correcto
                window.location.href = "index.php";
            } else
            {
                // ❌ Error de credenciales u otro
                document.getElementById("loginError").style.display = "block";
                document.getElementById("loginError").innerText =
                    data.error || "Credenciales inválidas";
            }
        } catch (err)
        {
            // ❌ Error de red o fetch
            console.error("Error de conexión:", err);
            document.getElementById("loginError").style.display = "block";
            document.getElementById("loginError").innerText =
                "Error de conexión con el servidor";
        }
    });
}
document.addEventListener("DOMContentLoaded", function () {

    // --- 1. PREVIEW FOTO PROFIL ---
    const profileInput = document.getElementById("profilePicInput");
    if (profileInput) {
        profileInput.addEventListener("change", function (e) {
            const preview = document.getElementById("profilePicPreview");
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    preview.src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // --- 2. LOGIKA SIGN UP (REGISTER) ---
    // Menggunakan '?.' agar tidak error jika dijalankan di halaman Login
    document.getElementById("signupForm")?.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Validasi sederhana
        if (!formData.get("username") || !formData.get("email") || !formData.get("phone") || !formData.get("password")) {
            alert("Semua field harus diisi!");
            return;
        }

        // Ubah tombol jadi loading
        const submitBtn = this.querySelector("button[type=submit]");
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = "Processing...";

        fetch("SignUp.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.text()) // PENTING: Ambil teks dulu
            .then(text => {
                try {
                    const result = JSON.parse(text); // Coba ubah ke JSON

                    if (result.status === "success") {
                        alert(result.message);
                        // Redirect ke halaman utama jika sukses
                        window.location.href = "../explore/homepage_1.php";
                    } else {
                        // Tampilkan error dari PHP (misal: Email kembar)
                        alert("Gagal: " + result.message);
                    }
                } catch (err) {
                    // JIKA GAGAL PARSE JSON (Berarti PHP Error Fatal/Crash)
                    console.error("Server Error Raw:", text);
                    alert("Terjadi Error Sistem:\n" + text.substring(0, 150) + "...");
                }
            })
            .catch(error => {
                console.error("Network Error:", error);
                alert("Gagal terhubung ke server.");
            })
            .finally(() => {
                // Kembalikan tombol
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
            });
    });

    // --- 3. LOGIKA LOGIN ---
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value.trim();
            const rememberElement = document.getElementById("rememberLogin");
            const remember = rememberElement ? rememberElement.checked : false;

            if (email === "" || password === "") {
                alert("Email dan password harus diisi!");
                return;
            }

            const data = {
                email: email,
                password: password,
                remember: remember
            };

            fetch("Login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(result => {
                    if (result.status === "success") {
                        alert(result.message);
                        window.location.href = "../explore/homepage_1.php";
                    } else {
                        alert(result.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Terjadi kesalahan saat login.");
                });
        });
    }
});
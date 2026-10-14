document.addEventListener('DOMContentLoaded', () => {
    const inputBuscador = document.getElementById('buscador');
    const boxSugerencias = document.getElementById('sugerencias-box');
    let timeoutId = null;

    if (!inputBuscador || !boxSugerencias) return;

    // Escuchar cuando el usuario escribe
    inputBuscador.addEventListener('input', () => {
        clearTimeout(timeoutId);
        const query = inputBuscador.value.trim();

        if (query.length < 2) {
            boxSugerencias.innerHTML = '';
            boxSugerencias.style.display = 'none';
            return;
        }

        // Debounce de 250ms para optimizar peticiones
        timeoutId = setTimeout(() => {
            fetch(`SugerenciasBusqueda.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    boxSugerencias.innerHTML = '';

                    if (data.length === 0) {
                        boxSugerencias.style.display = 'none';
                        return;
                    }

                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.classList.add('sugerencia-item');
                        div.textContent = item.nombre;
                    
                        // Estilos inline forzados para garantizar visibilidad y formato limpio
                        div.style.padding = '10px 15px';
                        div.style.color = '#222222';             // Texto oscuro
                        div.style.backgroundColor = '#ffffff';   // Fondo blanco
                        div.style.cursor = 'pointer';
                        div.style.fontSize = '0.9rem';
                        div.style.textAlign = 'left';
                        div.style.borderBottom = '1px solid #f0f0f0';
                    
                        // Efecto visual al pasar el mouse (hover)
                        div.addEventListener('mouseenter', () => {
                            div.style.backgroundColor = '#f0f4f8';
                            div.style.color = '#0f4c81';
                            div.style.fontWeight = 'bold';
                        });
                    
                        div.addEventListener('mouseleave', () => {
                            div.style.backgroundColor = '#ffffff';
                            div.style.color = '#222222';
                            div.style.fontWeight = 'normal';
                        });
                    
                        // Redirección al hacer clic en una sugerencia
                        div.addEventListener('click', () => {
                            window.location.href = `CarrerasUniversidades.php?id_carrera=${item.id}`;
                        });
                    
                        boxSugerencias.appendChild(div);
                    });

                    boxSugerencias.style.display = 'block';
                })
                .catch(err => console.error('Error fetching sugerencias:', err));
        }, 250);
    });

    // Ocultar la lista si el usuario hace clic fuera del buscador
    document.addEventListener('click', (e) => {
        if (!inputBuscador.contains(e.target) && !boxSugerencias.contains(e.target)) {
            boxSugerencias.style.display = 'none';
        }
    });
});
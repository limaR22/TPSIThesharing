document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('toggleMode');
    const body = document.body;
  
    // Carregar o tema salvo no localStorage (se existir)
    const savedTheme = localStorage.getItem('theme') || 'light-mode';
    body.classList.add(savedTheme);
  
    // Alterar o ícone do botão conforme o tema atual
    if (savedTheme === 'dark-mode') {
      toggleButton.innerHTML = '<i class="fas fa-sun"></i>';
    }
  
    // Evento de clique para alternar entre os temas
    toggleButton.addEventListener('click', () => {
      if (body.classList.contains('light-mode')) {
        body.classList.remove('light-mode');
        body.classList.add('dark-mode');
        toggleButton.innerHTML = '<i class="fas fa-sun"></i>';  // Alterar ícone para "sol"
        localStorage.setItem('theme', 'dark-mode');
      } else {
        body.classList.remove('dark-mode');
        body.classList.add('light-mode');
        toggleButton.innerHTML = '<i class="fas fa-moon"></i>';  // Alterar ícone para "lua"
        localStorage.setItem('theme', 'light-mode');
      }
    });
  });
  
document.addEventListener('DOMContentLoaded', () => {
    const botao = document.getElementById('btn-ataque');
    const cartao = document.getElementById('card-rayquaza');

    if (botao && cartao) {
        botao.addEventListener('click', () => {
            cartao.classList.add('ataque-animacao');

            setTimeout(() => {
                cartao.classList.remove('ataque-animacao');
            }, 600);
        });
    }
});
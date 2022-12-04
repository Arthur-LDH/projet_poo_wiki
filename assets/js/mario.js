class MulticoinBlock extends HTMLElement {
	constructor() {
		super();

		this.clicks = 0;
		this.maxClicks = 25;

		this.blockSvgUse = this.querySelector('.multicoin-block__block use');

		this.bind();
	}

	bind() {
		this.addEventListener('click', (e) => {
			if (this.clicks >= this.maxClicks) {
				return;
			}

			this.clicks++;
			this.addCoin();
			this.style.setProperty('animation-name', 'bounce');

			if (this.clicks >= this.maxClicks) {
				this.blockSvgUse.setAttribute('href', '#empty');
			}
		});

		this.addEventListener('animationend', (e) => {
			this.style.removeProperty('animation-name');
		});
	}

	addCoin() {
		const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
		const id = 'coin';
		const use = document.createElementNS('http://www.w3.org/2000/svg', 'use');

		use.setAttributeNS('http://www.w3.org/1999/xlink','href', '#' + id);

		svg.addEventListener('animationend', (e) => {
			svg.remove();
		});
		svg.appendChild(use);
		svg.classList.add('multicoin-block__coin');

		this.appendChild(svg);
	}
}

customElements.define('multicoin-block', MulticoinBlock);
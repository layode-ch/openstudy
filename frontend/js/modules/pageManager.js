import APIClient from "./apiClient.js";
import Page from "./page.js";

/**
 * Classe pour gérer les pages SPA.
 */
export default class PageManager {
	#location;
	/**
	 * @constructor
	 * @param {Map<string, Page>} route 
	 * @param {Element} main
	 * @param {boolean} [hash=true] 
	 * @param {string[]} publicPages 
	 * @param {string[]} adminPages
	 */
	constructor(route, main, hash = true, publicPages = [], adminPages = []) {
		this.route = route;
		this.main = main;
		this.hash = hash;
		window.addEventListener("popstate", e => {
			this.handleLocation();
		})
		this.publicPages = publicPages;
		this.adminPages = adminPages;
		this.#location = null;
	}

	/**
	 * Gère le changement de page selon l'URL.
	 * @returns {Promise<void>}
	 */
	async handleLocation() {
		const location = window.location;
		let path = this.hash ? location.hash.replace("#", "/") : location.pathname;
		if (path.trim() == "")
			path = "/";
		if (path === this.#location)
			return;
		if(!this.route.has(path)){
			console.error("Page not found");
			path = "/not-found";
		}
		if (!this.publicPages.includes(path) && !await APIClient.checkToken()) {
			this.changePage("/login");
			return;
		}
		const page = this.route.get(path);
		this.main.innerHTML = "";
		this.main.append(page.template.content.cloneNode(true));
		page.main = this.main;
		this.#location = window.location.pathname;
		page.init();
	}

	/**
	 * Change la page courante.
	 * @param {string} page
	 */
	changePage(page){
		let path = String(page);
		if (!path.startsWith("/")) path = "/" + path;
		if (this.hash)
			window.location.hash = path;
		else
			window.history.pushState({}, "", path);
		this.handleLocation();
	}

	reload() {
		let path = this.hash ? location.hash.replace("#", "/") : location.pathname;
		if (path.trim() == "")
			path = "/";
		
		const page = this.route.get(path);
		this.main.innerHTML = "";
		this.main.append(page.template.content.cloneNode(true));
		page.main = this.main;
		this.#location = window.location.pathname;
		page.init();
	}
}
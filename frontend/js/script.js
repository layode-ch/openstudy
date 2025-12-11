import Page from "./modules/page.js";
import PageManager from "./modules/pageManager.js";
import Login from "./pages/login.js";
import Sets from "./pages/sets.js";
import SetsEdit from "./pages/setsEdit.js";
import SetsFlashcard from "./pages/setsFlashcard.js";
import SignUp from "./pages/signup.js";

class App {
	constructor() {
		const route = new Map([
			["/", new Page("#home")],
			["/not-found", new Page("#not-found")],
			["/login", new Login("#login")],
			["/sign-up", new SignUp("#sign-up")],
			["/sets", new Sets("#sets")],
			["/sets/edit", new SetsEdit("#sets-edit")],
			["/sets/flashcard", new SetsFlashcard("#sets-flashcard")]
		]);
		const app = document.querySelector("#app");
		this.pageManager = new PageManager(route, app, false, ["/", "/login", "/sign-up"]);
		this.pageManager.handleLocation();
		this.#setupLinkEvents();
	}

	#setupLinkEvents() {
		document.addEventListener("click", (e) => {
		const link = e.target.closest("a"); // find nearest <a> element
		if (!link) return;

		const href = link.getAttribute("href");
		const target = link.getAttribute("target");

		// Only handle same-page internal links (ignore external links, targets like _blank)
		if (!href.startsWith("http") && target !== "_blank") {
			e.preventDefault(); // stop default reload
			app.pageManager.changePage(href); // tell your PageManager to change page
		}
		});
	}
}

const app = new App();

export default app;
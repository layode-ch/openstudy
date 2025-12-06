export default class APIClient {
	
	static API_BASE = "api";

	static get token() { return localStorage.getItem("token"); }
	static set token(value) { localStorage.setItem("token", value); }

	static getHeaders() {
		const headers = { "Content-Type": "application/json" };
		if (this.token) {
			headers["Authorization"] = `Bearer ${this.token}`;
		}
		return headers;
	}

	static async checkToken() { 
		const result = await this.request("GET", "/user/auth"); 
		return !(result instanceof APIError);
	}

	/**
	 * Effectue une requête générique à l'API.
	 * @param {string} method
	 * @param {string} endpoint
	 * @param {Object|null} data
	 * @param {boolean} includeAuth
	 * @returns {Promise<Object|APIError>}
	 */
	static async request(method, endpoint, data = null) {
		const url = `${this.API_BASE}${endpoint}`;
		const config = {
			method,
			headers: this.getHeaders()
		};

		if (data) {
			config.body = JSON.stringify(data);
		}

		try {
			const response = await fetch(url, config);
			const result = await response.json();

			// API returns an errors key -> wrap in APIError
			if (result && typeof result === "object" && result.errors) {
				return new APIError(result.errors);
			}

			if (!response.ok) {
				// If no structured errors, still wrap a generic error
				return new APIError([result.message || `HTTP ${response.status}`]);
			}

			return result;
		} catch (error) {
			console.error(`API Error [${method} ${endpoint}]:`, error);
			return new APIError([error.message || "Network error"]);
		}
	}

	static login(data) {
		return this.request("POST", "/user/login", data);
	}

	static signUp(data) {
		return this.request("POST", "/user/sign-up", data);
	}

}

export class APIError {
	/**
	 * @param {Array|string|Object} errors
	 */
	constructor(errors = []) {
		if (errors === null) errors = [];
		this.errors = Array.isArray(errors) ? errors : [errors];
	}

	/**
	 * Human readable message
	 * @returns {string}
	 */
	message() {
		return this.errors.map(e => (typeof e === "string" ? e : JSON.stringify(e))).join("\n");
	}
}
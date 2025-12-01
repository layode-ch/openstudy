// @ts-ignore
import { animate, waapi, eases, spring, JSAnimation } from 'animejs';


/**
 * Description placeholder
 *
 * @export
 * @class Alert
 * @extends {HTMLDivElement}
 */
export default class Alert extends HTMLDivElement {
    static get name() { return "alert-component"; }

    
    get message() { return this.querySelector("span").textContent; }
    set message(value) { this.querySelector("span").textContent = value; }

    get fade () { 
        const value = this.getAttribute("fade");
        return value !== null && value !== "false" 
    }
    set fade (value) { this.setAttribute("fade", value.toString()); }
    
    /**
     * Description placeholder
     *
     * @type {"infos" | "warning" | "success" | null}
     */
    get type() {
        // @ts-ignore
        return this.getAttribute("type");
    }
    set type(value) {
        this.classList.toggle(`alert-${value}`, value !== null);
        this.setAttribute("type", value);
    }
    
    constructor() {
        super();
        this.type = this.type;
        this.type = "infos";
        this.classList.add("alert");
        const text = this.textContent;
        this.innerHTML = `
			<i class="bi bi-info-circle"></i>
                <span>${text}</span>
            <button class="alert-close" style="cursor: pointer;">
                <i class="bi bi-x-lg"></i>
            </button>
		`;
        this.closeBtn = this.querySelector("button.alert-close");
        this.closeBtn.addEventListener("click", this.close.bind(this))
        this.animation;
    }

    connectedCallback() {
        const opacity = this.fade ? 0 : 1;
        this.animation = animate(this, {
            opacity: { from: opacity, 
            duration: 300}
        });
    }

    close() {
        this.animation.onComplete = () => {
            this.remove();
        }
        this.animation.reverse();
    }

    /**
     * Creates an instance of `Alert`
     *
     * @param {string} message 
     * @static
     * @returns {Alert}
     */
    static create(message) {
        /** @type {Alert} */
        // @ts-ignore
        const alert = document.createElement("div", { is: Alert.name });
        alert.message = message;
        return alert;
    }
}
customElements.define(Alert.name, Alert, { extends: "div" });
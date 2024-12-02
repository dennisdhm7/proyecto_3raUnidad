export default class Toast {
    constructor(element, options) {
        this.element = element;
        // if (options.delay != undefined) {
        //     setTimeout(() => {
        //         this.element.classList.remove("show");
        //     }, options.delay + 500);
        // }
    }

    show() {
        this.element.classList.add("show");
    }
}

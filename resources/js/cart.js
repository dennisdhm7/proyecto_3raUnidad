export default class Cart {
    constructor(emitter) {
        this.emitter = emitter;
        this.order = {
            items: [],
        };
        this.load();
    }

    load() {
        const data = localStorage.getItem("order");
        if (data != null) {
            this.order = JSON.parse(data);
        }
    }

    add(data, quantity, combination) {
        for (let item of this.order.items) {
            if (item.item_id == data.id) {
                if (
                    combination == item.combination_id ||
                    (combination != null &&
                        item.combination_id == combination.id)
                ) {
                    console.log(quantity)
                    item.quantity += quantity;
                    this.save();
                    return;
                }
            }
        }
        this.order.items.push({
            item_id: data.id,
            item: data,
            quantity: quantity,
            combination_id: combination == null ? null : combination.id,
            combination: combination,
        });
        this.save();
    }

    update(index, quantity) {
        this.order.items[index].quantity = quantity;
        this.save();
    }

    remove(index) {
        this.order.items.splice(index, 1)
        this.save();
    }

    save() {
        localStorage.setItem("order", JSON.stringify(this.order));
        this.emitter.emit("cart-updated", this.order);
    }

    get total() {
        let total = 0;
        for (let item of this.order.items) {
            total += Number(item.item.local_price) * item.quantity;
        }
        return total;
    }
}

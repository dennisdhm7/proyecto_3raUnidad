<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use App\Models\State;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //Importar monedas
        $currencies = json_decode(File::get("database/seeders/json/currencies.json"), true);
        DB::table('currencies')->insert($currencies);

        DB::table('business_hours')->insert([
            ['day' => 0, 'start' => '09:00', 'end' => '23:00', 'closed' => false],
            ['day' => 1, 'start' => '09:00', 'end' => '23:00', 'closed' => false],
            ['day' => 2, 'start' => null, 'end' => null, 'closed' => true],
            ['day' => 3, 'start' => '09:00', 'end' => '23:00', 'closed' => false],
            ['day' => 4, 'start' => '09:00', 'end' => '23:00', 'closed' => false],
            ['day' => 5, 'start' => '09:00', 'end' => '23:00', 'closed' => false],
            ['day' => 6, 'start' => '09:00', 'end' => '23:00', 'closed' => false],
        ]);

        DB::table('permissions')->insert([
            ['name' => 'Ver estadísticas', 'module' => 'account', 'submodule' => 'dashboard'],
            ['name' => 'Modificar datos del negocio', 'module' => 'account', 'submodule' => 'dashboard'],
            ['name' => 'Ver usuarios', 'module' => 'account', 'submodule' => 'users'],
            ['name' => 'Administrar usuarios', 'module' => 'account', 'submodule' => 'users'],
            ['name' => 'Ver categorías', 'module' => 'menu', 'submodule' => 'categories'],
            ['name' => 'Agregar y editar categorías', 'module' => 'menu', 'submodule' => 'categories'],
            ['name' => 'Eliminar categorías', 'module' => 'menu', 'submodule' => 'categories'],
            ['name' => 'Ver productos', 'module' => 'menu', 'submodule' => 'items'],
            ['name' => 'Agregar y editar productos', 'module' => 'menu', 'submodule' => 'items'],
            ['name' => 'Eliminar productos', 'module' => 'menu', 'submodule' => 'items'],
            ['name' => 'Ver pedidos', 'module' => 'manager', 'submodule' => 'orders'],
            ['name' => 'Agregar pedidos', 'module' => 'manager', 'submodule' => 'orders'],
            ['name' => 'Editar pedidos', 'module' => 'manager', 'submodule' => 'orders'],
            ['name' => 'Cancelar pedidos', 'module' => 'manager', 'submodule' => 'orders'],
            ['name' => 'Ver reservaciones', 'module' => 'manager', 'submodule' => 'reservations'],
            ['name' => 'Administrar reservaciones', 'module' => 'manager', 'submodule' => 'reservations'],
        ]);

        DB::table('users')->insert([
            'name' => 'Marcos López',
            'phone' => '+5358091603',
            'email' => 'msomnium.info@gmail.com',
            'password' => bcrypt('gWCXR3Y8'),
            'is_admin' => true,
            'role' => 'Administrator'
        ]);

        DB::table('categories')->insert([
            ['name' => 'Desayunos', 'description' => 'Ligeros y exquisito, comienza el día con uno de nustros desayunos.',],
            ['name' => 'Entrantes', 'description' => 'Lo mejor que puedes darle a tu paladar mientras esperas lo grande.',],
            ['name' => 'Pizzas', 'description' => 'Nuestras pizzas son resultado de la fusión de Italia y Cuba, lo máximo.',],
            ['name' => 'Hamburguesas', 'description' => 'Un pan suave y dorado y el interior una mezcla de sabores deliciosos, ese es el secreto de nuestras hamburguesas',],
            ['name' => 'Pastas', 'description' => 'Queso, mucho queso. La salsa es la base de todo, por eso la preparamos nosotros mismos. ¡Anímate a probarla!',],
            ['name' => 'Postres', 'description' => 'Amantes de lo sublime, este es su paraíso. Tenemos variadas ofertas de postres, tanto tradicionales como recetas propias.',],
            ['name' => 'Cocteles', 'description' => 'Acompaña tu comida con nuestros cocteles. O solo disfruta de ellos en buena compañía.',],
            ['name' => 'Bebidas', 'description' => 'No puedes irte sin beber algo de nuestro bar restaurant. ¡Échale un vistazo a nuestro menú!',],
            ['name' => 'Ceviches', 'description' => 'Consectetur culpa nostrud veniam amet laboris eiusmod.',],
            ['name' => 'Empaques', 'description' => 'Y si quieres llevarte algo a casa, nuestros empaques son tu mejor aliado, elige el que más te acomode.',],
        ]);

        $items = array(
            array('id' => '1', 'image' => 'src/items/croquetas-de-pollo-7850.webp', 'aux_image' => 'src/items/croquetas-de-pollo-7850.png', 'name' => 'Croquetas de pollo', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '2', 'local_price' => '125.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '0', 'active' => '1', 'created_at' => '2024-11-27 18:20:21', 'updated_at' => '2024-11-27 18:20:34'),
            array('id' => '2', 'image' => 'src/items/choto-al-ajillo-47845.webp', 'aux_image' => 'src/items/choto-al-ajillo-47845.png', 'name' => 'Choto al ajillo', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '2', 'local_price' => '423.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:21:17', 'updated_at' => '2024-11-27 18:21:17'),
            array('id' => '3', 'image' => 'src/items/desayuno-continental-76965.webp', 'aux_image' => 'src/items/desayuno-continental-76965.png', 'name' => 'Desayuno continental', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '1', 'local_price' => '478.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:22:09', 'updated_at' => '2024-11-27 18:22:09'),
            array('id' => '4', 'image' => 'src/items/pan-de-muerto-7225.webp', 'aux_image' => 'src/items/pan-de-muerto-7225.png', 'name' => 'Pan de muerto', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '6', 'local_price' => '86.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '1', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:22:50', 'updated_at' => '2024-11-27 18:22:50'),
            array('id' => '5', 'image' => 'src/items/arroz-belicenho-58335.webp', 'aux_image' => 'src/items/arroz-belicenho-58335.png', 'name' => 'Arroz beliceño', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '1', 'local_price' => '470.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '1', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:23:27', 'updated_at' => '2024-11-27 18:23:27'),
            array('id' => '6', 'image' => 'src/items/ajiaco-cubano-40174.webp', 'aux_image' => 'src/items/ajiaco-cubano-40174.png', 'name' => 'Ajiaco cubano', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '2', 'local_price' => '458.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:24:02', 'updated_at' => '2024-11-27 18:24:02'),
            array('id' => '7', 'image' => 'src/items/bizcocho-94621.webp', 'aux_image' => 'src/items/bizcocho-94621.png', 'name' => 'Bizcocho', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '6', 'local_price' => '125.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '1', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:24:42', 'updated_at' => '2024-11-27 18:24:42'),
            array('id' => '8', 'image' => 'src/items/bistec-de-cerdo-a-la-parrilla-61749.webp', 'aux_image' => 'src/items/bistec-de-cerdo-a-la-parrilla-61749.png', 'name' => 'Bistec de cerdo a la parrilla', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '2', 'local_price' => '450.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:25:19', 'updated_at' => '2024-11-27 18:25:19'),
            array('id' => '9', 'image' => 'src/items/zumo-de-hierbabuena-73398.webp', 'aux_image' => 'src/items/zumo-de-hierbabuena-73398.png', 'name' => 'Zumo de hierbabuena', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '8', 'local_price' => '45.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '1', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:25:50', 'updated_at' => '2024-11-27 18:25:50'),
            array('id' => '10', 'image' => 'src/items/te-moro-6711.webp', 'aux_image' => 'src/items/te-moro-6711.png', 'name' => 'Te moro', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '8', 'local_price' => '35.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '1', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:26:15', 'updated_at' => '2024-11-27 18:26:15'),
            array('id' => '11', 'image' => 'src/items/mojito-cubano-24258.webp', 'aux_image' => 'src/items/mojito-cubano-24258.png', 'name' => 'Mojito cubano', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '7', 'local_price' => '150.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '1', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:26:44', 'updated_at' => '2024-11-27 18:26:44'),
            array('id' => '12', 'image' => 'src/items/licores-28052.webp', 'aux_image' => 'src/items/licores-28052.png', 'name' => 'Licores', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '8', 'local_price' => '450.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:27:06', 'updated_at' => '2024-11-27 18:27:06'),
            array('id' => '13', 'image' => 'src/items/caipirinha-34588.webp', 'aux_image' => 'src/items/caipirinha-34588.png', 'name' => 'Caipiriña', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '7', 'local_price' => '548.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '0', 'active' => '1', 'created_at' => '2024-11-27 18:27:30', 'updated_at' => '2024-11-27 18:27:30'),
            array('id' => '14', 'image' => 'src/items/caipiroska-57988.webp', 'aux_image' => 'src/items/caipiroska-57988.png', 'name' => 'Caipiroska', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '7', 'local_price' => '150.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '0', 'active' => '1', 'created_at' => '2024-11-27 18:28:18', 'updated_at' => '2024-11-27 18:28:18'),
            array('id' => '15', 'image' => 'src/items/hamburguesa-y-papas-fritas-24240.webp', 'aux_image' => 'src/items/hamburguesa-y-papas-fritas-24240.png', 'name' => 'Hamburguesa y papas fritas', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '4', 'local_price' => '500.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:28:54', 'updated_at' => '2024-11-27 18:28:54'),
            array('id' => '16', 'image' => 'src/items/hamburguesa-simple-88233.webp', 'aux_image' => 'src/items/hamburguesa-simple-88233.png', 'name' => 'Hamburguesa simple', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '4', 'local_price' => '450.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:29:22', 'updated_at' => '2024-11-27 18:29:22'),
            array('id' => '17', 'image' => 'src/items/big-mac-25035.webp', 'aux_image' => 'src/items/big-mac-25035.png', 'name' => 'Big mac', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '4', 'local_price' => '460.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:29:43', 'updated_at' => '2024-11-27 18:29:43'),
            array('id' => '18', 'image' => 'src/items/pack-de-hamburguesas-74154.webp', 'aux_image' => 'src/items/pack-de-hamburguesas-74154.png', 'name' => 'Pack de hamburguesas', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '4', 'local_price' => '890.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:30:09', 'updated_at' => '2024-11-27 18:30:09'),
            array('id' => '19', 'image' => 'src/items/pizza-de-cebolla-54118.webp', 'aux_image' => 'src/items/pizza-de-cebolla-54118.png', 'name' => 'Pizza de cebolla', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '3', 'local_price' => '680.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:30:31', 'updated_at' => '2024-11-27 18:30:31'),
            array('id' => '20', 'image' => 'src/items/pizza-de-peproni-45631.webp', 'aux_image' => 'src/items/pizza-de-peproni-45631.png', 'name' => 'Pizza de peproni', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '3', 'local_price' => '780.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:30:53', 'updated_at' => '2024-11-27 18:30:53'),
            array('id' => '21', 'image' => 'src/items/pizza-de-queso-blanco-2316.webp', 'aux_image' => 'src/items/pizza-de-queso-blanco-2316.png', 'name' => 'Pizza de queso blanco', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '3', 'local_price' => '600.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:31:26', 'updated_at' => '2024-11-27 18:31:26'),
            array('id' => '22', 'image' => 'src/items/pizza-seca-14774.webp', 'aux_image' => 'src/items/pizza-seca-14774.png', 'name' => 'Pizza seca', 'description' => 'Lorem amet proident eiusmod exercitation aliquip amet ad est nulla aute sunt elit velit eiusmod. Mollit consectetur sunt tempor fugiat eu ex.', 'features' => NULL, 'category_id' => '3', 'local_price' => '500.00', 'local_offer' => NULL, 'foreign_price' => NULL, 'foreign_offer' => NULL, 'likes' => '0', 'vegan' => '0', 'delivery' => '1', 'active' => '1', 'created_at' => '2024-11-27 18:31:51', 'updated_at' => '2024-11-27 18:31:51')
        );

        DB::table('items')->insert($items);
    }
}

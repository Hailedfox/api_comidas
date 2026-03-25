<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id(); // Crea la columna 'id' autoincrementable
            $table->unsignedBigInteger('id_comercio');
            $table->unsignedBigInteger('id_categoria')->nullable();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_original', 8, 2);
            $table->decimal('precio_descuento', 8, 2);
            $table->integer('cantidad_disponible');
            $table->date('fecha_caducidad');
            $table->time('hora_recogida_inicio')->nullable();
            $table->time('hora_recogida_fin')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('foto')->nullable();
            $table->timestamps(); // Crea 'created_at' y 'updated_at'
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

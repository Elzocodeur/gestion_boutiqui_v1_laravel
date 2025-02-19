<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('dettes', function (Blueprint $table) {
//             $table->id();
//             $table->decimal('montant', 8, 2); // c'est
//             $table->decimal('montantDu', 8, 2);
//             $table->decimal('montantRestant', 8, 2);
//             $table->unsignedBigInteger('client_id');
//             $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('dettes');
//     }
// };





use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 8, 2);
            $table->decimal('montantRestant', 8, 2);
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dettes');
    }
};

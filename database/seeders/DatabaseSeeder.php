<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(DocumentoSeeder::class);
        $this->call(ComprobanteSeeder::class);
        $this->call(PaisSeeder::class);
        $this->call(DepartamentoSeeder::class);
        $this->call(MunicipioSeeder::class);
        $this->call(AfiliacionSeeder::class);
        $this->call(SucursalSeeder::class);
        $this->call(SucursalUsuarioSeeder::class);
        $this->call(TiposDteSeeder::class);
        $this->call(ImpuestosTipoSeeder::class);
        $this->call(ImpuestosUnidadGravableSeeder::class);
        $this->call(FrasesSeeder::class);
        $this->call(EscenariosSeeder::class);
        $this->call(MonedasSeeder::class);
        //$this->call(CategoriasSeeder::class);
        //$this->call(MarcasSeeder::class);
        $this->call(CertificadoresSeeder::class);
        $this->call(MetodosPagoSeeder::class);
        //$this->call(ClientesSeeder::class);
        //$this->call(ProveedoresSeeder::class);
        //$this->call(ProductosSeeder::class);
    }
}

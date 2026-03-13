<?php

namespace Database\Seeders;

use App\Models\Frase;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FrasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frases = [
            ['id' => 1, 'nombre' => 'Frases de retención del ISR', 'descripcion' => 'Indica el régimen al que se encuentra inscrito el EMISOR para que el RECEPTOR sepa si debe efectuarle o no la respectiva retención. Estas frases deben venir atómicamente del Registro Tributario Unificado según la afiliación de ISR que tenga el Emisor'],
            ['id' => 2, 'nombre' => 'Frases de Agente de retención del IVA', 'descripcion' => 'Indica si el EMISOR es Agente de Retención del IVA. Si el RECEPTOR también lo es sabrá que no debe efectuar la respectiva retención. Esta frase debe venir automáticamente del Registro Tributario Unificado según la calidad de AGENTE DE RETENCIÓN que tenga el Emisor.'],
            ['id' => 3, 'nombre' => 'Frases de no genera derecho a crédito fiscal del IVA', 'descripcion' => 'Cuando el emisor se encuentre afiliado al régimen de pequeño contribuyente, para que el receptor sepa que la factura no genera derecho a crédito fiscal. Esta frase debe venir atómicamente del Registro Tributario Unificado según la afiliación del IVA que tenga el Emisor'],
            ['id' => 4, 'nombre' => 'Frases de exento o no afecto al IVA', 'descripcion' => 'Existen facturas que por diferentes motivos: a) NO deben incluir el IVA. b) Deben incluir la base legal por la cual no incluyen el IVA (art. 29 literal “a” Ley del IVA y art. 11 Reglamento de la Ley del IVA). En los recibos y recibos por donación la correspondiente base legal de exención de IVA.'],
            ['id' => 5, 'nombre' => 'Frases de facturas especiales', 'descripcion' => 'En las compras en las que el emisor o prestador del servicio se niegue a emitir la factura correspondiente.'],
            ['id' => 6, 'nombre' => 'Frases de contribuyente agropecuario', 'descripcion' => 'Cuando el emisor se encuentre afiliado al régimen especial de contribuyente agropecuario, para que el receptor sepa cuando no debe efectuarle retención. Esta frase debe venir automáticamente del Registro Tributario Unificado basada en la afiliación del IVA del Emisor.  Artículo 54 “A” Ley del IVA'],
            ['id' => 7, 'nombre' => 'Frases de regímenes electrónicos', 'descripcion' => 'Cuando el emisor se encuentre afiliado al régimen electrónico especial de pequeño contribuyente o  contribuyente agropecuario, para que el receptor sepa que no debe efectuarle retención. Esta frase debe venir automáticamente del Registro Tributario Unificado basada en la afiliación del IVA del Emisor.  Artículo 54 “E” Ley del IVA'],
            ['id' => 8, 'nombre' => 'Frases de exento de ISR', 'descripcion' => 'Cuando el emisor se encuentra exento del Impuesto Sobre la Renta (ISR).'],
            ['id' => 9, 'nombre' => 'Frases especiales', 'descripcion' => 'Cuando el emisor efectúe operaciones especificas como por ejemplo apoyo social al Gas propano y apoyo social al diésel o gasolinas regular y superior.'],
        ];

        Frase::insert($frases);
    }
}

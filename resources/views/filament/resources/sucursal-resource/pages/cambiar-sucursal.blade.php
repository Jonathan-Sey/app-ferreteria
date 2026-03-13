<x-filament-panels::page>
    <div class="p-6">
        <!-- <h2 class="text-2xl font-semibold mb-4">Cambiar Sucursal</h2> -->

        @if (session()->has('message'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('message') }}
        </div>
        @endif

        @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
        @endif

        <div class="mb-4">
            <h3 class="text-lg font-medium">Sucursal Actual:</h3>
            @if ($sucursalActual)
            <p class="text-md">{{ $sucursalActual->nombre }}</p>
            @else
            <p class="text-md text-gray-500">No hay sucursal seleccionada.</p>
            @endif
        </div>

        <form wire:submit.prevent="cambiarSucursal">
            <div class="mb-4">
                <label for="sucursal_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Seleccionar Sucursal
                </label>
                <select id="sucursal_id" wire:model="sucursal_id"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 
               dark:bg-gray-800 dark:text-white focus:outline-none focus:ring-indigo-500 
               focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">-- Selecciona una sucursal --</option>
                    @foreach ($sucursales as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
                @error('sucursal_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Cambiar Sucursal
            </button>
            <button type="button" class="btn btn-warning">
                Cargar Archivo Word
            </button>
        </form>
    </div>
</x-filament-panels::page>
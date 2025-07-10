<div>
    <button wire:click="toggleModal" class="bg-blue-500 text-white px-4 py-2 rounded">
        {{ $showModal ? 'Fermer Modal' : 'Ouvrir Modal' }}
    </button>

    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded">
                <h2>Test Modal</h2>
                <button wire:click="closeModal" class="bg-red-500 text-white px-4 py-2 rounded mt-4">
                    Fermer
                </button>
            </div>
        </div>
    @endif
</div>
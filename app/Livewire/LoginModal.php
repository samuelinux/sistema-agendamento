<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class LoginModal extends Component
{
    public bool $open = false;

    // controla exibição do campo "Nome" (usuário novo)
    public bool $novoUsuario = false;

    #[On('open-login')]
    public function open(): void
    {
        // se quiser, pode ler da sessão:
        $this->novoUsuario = (bool) session('new_user', false);
        $this->open = true;
    }

    #[On('close-login')]
    public function close(): void
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.login-modal');
    }
}

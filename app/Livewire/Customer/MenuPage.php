<?php

namespace App\Livewire\Customer;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.customer')]
#[Title('Menu - Geprek Rejo')]
class MenuPage extends Component
{
    /** Currently filtered category (null = semua) */
    public ?int $activeCategory = null;

    /** Live search query */
    public string $searchQuery = '';

    /** Selected menu item ID for detail modal */
    public ?int $selectedMenuId = null;

    public function showMenuDetail(int $menuItemId): void
    {
        $this->selectedMenuId = $menuItemId;
    }

    public function closeMenuDetail(): void
    {
        $this->selectedMenuId = null;
    }

    // ── Lifecycle ────────────────────────────────────────────────────────────────

    // Member properties
    public ?int    $loggedInMemberId = null;
    public ?string $loggedInMemberName = null;
    public int     $loggedInMemberPoints = 0;

    // Toggle for Member Area Modal
    public bool    $showMemberModal = false;
    public bool    $isRegistering = false;

    // Login fields
    public string  $memberPhone = '';
    public string  $memberPin = '';
    public string  $memberLoginError = '';

    // Registration fields
    public string  $registerName = '';
    public string  $registerPhone = '';
    public string  $registerPin = '';
    public string  $registerPin_confirmation = '';
    public string  $memberRegisterError = '';

    // ── Lifecycle ────────────────────────────────────────────────────────────────

    public function mount(Request $request): void
    {
        // Simpan QR source code ke session jika ada di URL (?src=...)
        if ($src = $request->query('src')) {
            session(['qr_source' => $src]);
        }

        $this->loadMemberSession();
    }

    public function loadMemberSession(): void
    {
        $memberId = session('checkout_member_id');
        if ($memberId) {
            $member = \App\Models\Member::find($memberId);
            if ($member && $member->is_active) {
                // Auto redeem check
                if ($member->points >= 150) {
                    app(\App\Services\PointService::class)->checkAndAutoRedeemReward($member);
                    $member->refresh();
                }

                $this->loggedInMemberId = $member->id;
                $this->loggedInMemberName = $member->name;
                $this->loggedInMemberPoints = $member->points;
            } else {
                $this->logoutMember();
            }
        } else {
            $this->loggedInMemberId = null;
            $this->loggedInMemberName = null;
            $this->loggedInMemberPoints = 0;
        }
    }

    public function loginMember(): void
    {
        $this->memberLoginError = '';

        if (empty($this->memberPhone) || empty($this->memberPin)) {
            $this->memberLoginError = 'Nomor HP dan PIN wajib diisi.';
            return;
        }

        $member = \App\Models\Member::active()->byPhone($this->memberPhone)->first();

        if (! $member || ! \Illuminate\Support\Facades\Hash::check($this->memberPin, $member->pin)) {
            $this->memberLoginError = 'Nomor HP atau PIN salah.';
            return;
        }

        session(['checkout_member_id' => $member->id]);
        $this->loadMemberSession();

        $this->showMemberModal = false;
        $this->memberPhone = '';
        $this->memberPin = '';
    }

    public function registerMember(): void
    {
        $this->memberRegisterError = '';

        $this->validate([
            'registerName'              => 'required|string|min:2|max:150',
            'registerPhone'             => 'required|numeric|digits_between:10,15|unique:members,phone',
            'registerPin'               => 'required|numeric|digits:6|confirmed',
            'registerPin_confirmation'  => 'required',
        ], [
            'registerName.required' => 'Nama lengkap wajib diisi.',
            'registerPhone.required' => 'Nomor HP wajib diisi.',
            'registerPhone.numeric' => 'Nomor HP harus berupa angka.',
            'registerPhone.digits_between' => 'Nomor HP harus terdiri dari 10-15 digit.',
            'registerPhone.unique' => 'Nomor HP sudah terdaftar sebagai member.',
            'registerPin.required' => 'PIN wajib diisi.',
            'registerPin.numeric' => 'PIN harus berupa angka.',
            'registerPin.digits' => 'PIN harus terdiri dari 6 digit.',
            'registerPin.confirmed' => 'Konfirmasi PIN tidak cocok.',
            'registerPin_confirmation.required' => 'Konfirmasi PIN wajib diisi.',
        ]);

        try {
            $member = \App\Models\Member::create([
                'name'         => $this->registerName,
                'phone'        => $this->registerPhone,
                'pin'          => $this->registerPin,
                'points'       => 0,
                'is_active'    => true,
                'total_orders' => 0,
                'total_spent'  => 0,
                'tier'         => 'bronze',
            ]);

            session(['checkout_member_id' => $member->id]);
            $this->loadMemberSession();

            $this->showMemberModal = false;
            $this->isRegistering = false;

            // Reset inputs
            $this->registerName = '';
            $this->registerPhone = '';
            $this->registerPin = '';
            $this->registerPin_confirmation = '';

            session()->flash('status', 'Pendaftaran member berhasil dan otomatis masuk!');
        } catch (\Throwable $e) {
            $this->memberRegisterError = 'Pendaftaran gagal: ' . $e->getMessage();
        }
    }

    public function logoutMember(): void
    {
        session()->forget('checkout_member_id');
        $this->loggedInMemberId = null;
        $this->loggedInMemberName = null;
        $this->loggedInMemberPoints = 0;
        $this->showMemberModal = false;
    }

    public function closeRewardPopup(): void
    {
        session()->forget('reward_vouchers_redeemed');
    }

    // ── Cart Actions ─────────────────────────────────────────────────────────────

    /** Tambah item ke cart, atau increment qty jika sudah ada */
    public function addToCart(int $menuItemId): void
    {
        $item = MenuItem::available()->findOrFail($menuItemId);
        $cart = session('cart', []);

        if (isset($cart[$menuItemId])) {
            $cart[$menuItemId]['quantity']++;
            $cart[$menuItemId]['subtotal'] = $cart[$menuItemId]['quantity'] * $cart[$menuItemId]['price'];
        } else {
            $cart[$menuItemId] = [
                'id'       => $item->id,
                'name'     => $item->name,
                'price'    => (float) $item->price,
                'quantity' => 1,
                'subtotal' => (float) $item->price,
                'image'    => $item->image,
            ];
        }

        session(['cart' => $cart]);
    }

    /** Hapus item dari cart sepenuhnya */
    public function removeFromCart(int $menuItemId): void
    {
        $cart = session('cart', []);
        unset($cart[$menuItemId]);
        session(['cart' => $cart]);
    }

    /** Increment qty item yang sudah ada di cart */
    public function increaseQty(int $menuItemId): void
    {
        $cart = session('cart', []);

        if (isset($cart[$menuItemId])) {
            $cart[$menuItemId]['quantity']++;
            $cart[$menuItemId]['subtotal'] = $cart[$menuItemId]['quantity'] * $cart[$menuItemId]['price'];
            session(['cart' => $cart]);
        }
    }

    /** Decrement qty; hapus item jika qty mencapai 0 */
    public function decreaseQty(int $menuItemId): void
    {
        $cart = session('cart', []);

        if (isset($cart[$menuItemId])) {
            $cart[$menuItemId]['quantity']--;

            if ($cart[$menuItemId]['quantity'] <= 0) {
                unset($cart[$menuItemId]);
            } else {
                $cart[$menuItemId]['subtotal'] = $cart[$menuItemId]['quantity'] * $cart[$menuItemId]['price'];
            }

            session(['cart' => $cart]);
        }
    }

    /** Reset semua filter (search + kategori) */
    public function clearFilters(): void
    {
        $this->searchQuery = '';
        $this->activeCategory = null;
    }

    // ── Computed Properties ──────────────────────────────────────────────────────

    /** Selected menu item for details modal */
    #[Computed]
    public function selectedMenuItem(): ?\App\Models\MenuItem
    {
        if (! $this->selectedMenuId) {
            return null;
        }
        return \App\Models\MenuItem::available()->with('category')->find($this->selectedMenuId);
    }

    /** Isi cart dari session */
    #[Computed]
    public function cart(): array
    {
        return session('cart', []);
    }

    /** Total jumlah item (sum of quantities) */
    #[Computed]
    public function cartCount(): int
    {
        return (int) array_sum(array_column(session('cart', []), 'quantity'));
    }

    /** Total harga cart (sum of subtotals) */
    #[Computed]
    public function cartTotal(): float
    {
        return (float) array_sum(array_column(session('cart', []), 'subtotal'));
    }

    /** Menu items dengan filter kategori + search, scope available + ordered */
    #[Computed]
    public function menuItems()
    {
        return MenuItem::available()
            ->ordered()
            ->with('category')
            ->when($this->activeCategory, fn ($q) => $q->where('category_id', $this->activeCategory))
            ->when(
                trim($this->searchQuery),
                fn ($q) => $q->where('name', 'like', '%' . trim($this->searchQuery) . '%')
            )
            ->get();
    }

    /** Semua kategori aktif, terurut */
    #[Computed]
    public function categories()
    {
        return Category::active()->ordered()->get();
    }

    // ── Navigation ───────────────────────────────────────────────────────────────

    /** Redirect ke halaman checkout, atau flash error jika cart kosong */
    public function goToCheckout(): void
    {
        if (empty(session('cart', []))) {
            session()->flash('cart_error', 'Keranjang masih kosong. Pilih menu dulu ya! 🛒');

            return;
        }

        $this->redirect(route('order.checkout'), navigate: true);
    }
}

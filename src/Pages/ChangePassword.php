<?php

namespace Mortezamasumi\FbPasswd\Pages;

use Filament\Actions\Action;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Exceptions\Halt;
use Filament\Support\Facades\FilamentView;
use Filament\Panel;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Js;
use Illuminate\Validation\Rules\Password;
use Mortezamasumi\FbPasswd\Middleware\ForcePasswordChangeMiddleware;
use Throwable;

use function Filament\Support\is_app_url;

class ChangePassword extends BaseEditProfile
{
    protected Width|string|null $maxWidth = 'md';
    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string|Htmlable
    {
        return __('password-change::password-change.title');
    }

    public function hasLogo(): bool
    {
        return true;
    }

    public static function routes(Panel $panel): void
    {
        Route::get(static::getRoutePath($panel), static::class)
            ->withoutMiddleware(ForcePasswordChangeMiddleware::class)
            ->name('auth.change-password');
    }

    public static function getRoutePath(Panel $panel): string
    {
        return '/change-password';
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(fn (): string => __('password-change::password-change.action'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function getCancelFormAction(): Action
    {
        if (! Auth::user()->force_change_password) {
            return Action::make('back')
                ->label(__('filament-panels::auth/pages/edit-profile.actions.cancel.label'))
                ->alpineClickHandler('document.referrer ? window.history.back() : (window.location.href = '.Js::from(filament()->getUrl()).')')
                ->color('gray');
        }

        return Action::make('back')
            ->label(__('filament-panels::layout.actions.logout.label'))
            ->action(function () {
                Auth::logout();

                redirect(Filament::getCurrentPanel()->getPath());
            })
            ->color('gray');
    }

    protected function getLayoutData(): array
    {
        return [
            'hasTopbar' => ! Auth::user()->force_change_password && $this->hasTopBar(),
            'maxWidth' => $this->getMaxWidth(),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(1)
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('password-change::password-change.current_password'))
                            ->required()
                            ->password()
                            ->revealable(Filament::arePasswordsRevealable())
                            ->rule('current_password')
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->autocomplete(false),
                        TextInput::make('password')
                            ->label(__('filament-panels::auth/pages/register.form.password.label'))
                            ->required()
                            ->password()
                            ->revealable(Filament::arePasswordsRevealable())
                            ->rule(Password::default(), App::isProduction())
                            ->different('current_password')
                            ->same('password_confirmation')
                            ->maxLength(255)
                            ->validationAttribute(__('filament-panels::auth/pages/register.form.password.validation_attribute'))
                            ->autocomplete(false),
                        TextInput::make('password_confirmation')
                            ->label(__('filament-panels::auth/pages/register.form.password_confirmation.label'))
                            ->requiredWith('password')
                            ->password()
                            ->revealable(Filament::arePasswordsRevealable())
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->autocomplete(false),
                    ]),
            ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $data['force_change_password'] = false;

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getUser(), $data);

            $this->callHook('afterSave');

            $this->commitDatabaseTransaction();
        } catch (Halt $exception) {
            $exception->shouldRollbackDatabaseTransaction()
                ? $this->rollBackDatabaseTransaction()
                : $this->commitDatabaseTransaction();

            return;
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        $this->getSavedNotification()?->send();

        if (Request::hasSession()) {
            Auth::logout();

            Session::invalidate();

            Session::regenerateToken();
        }

        $this->redirect(Filament::getCurrentPanel()->getLoginUrl(), navigate: FilamentView::hasSpaMode() && is_app_url(Filament::getCurrentPanel()->getLoginUrl()));
    }

    protected function getSavedNotification(): ?FilamentNotification
    {
        return FilamentNotification::make()
            ->success()
            ->title(__('password-change::password-change.notification'));
    }
}

<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Composers\ResponseComposer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\UpdateRequest;
use App\Models\Setting;
use App\Services\Admin\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\Factory as ViewFactory;

final class SettingController extends Controller
{
    protected const ACTION_INDEX = 'admin.settings.index';
    protected const VIEW_INDEX = 'admin.settings.index';
    protected const VIEW_EDIT = 'admin.settings.edit';
    protected const SETTING_UPDATED_KEY = 'messages.setting.updated';
    protected const SUCCESS_KEY = 'success';

    public function index(ViewFactory $view, SettingService $service): View
    {
        return $view->make(self::VIEW_INDEX, [
            'settings' => $service->getAll()
        ]);
    }

    public function edit(Setting $setting, ViewFactory $view): View
    {
        return $view->make(self::VIEW_EDIT, [
            'setting' => $setting
        ]);
    }

    public function update(UpdateRequest $request, Setting $setting, SettingService $service, ResponseComposer $composer): RedirectResponse
    {
        $service->update($setting, $request->validated());

        return $composer->redirect->route(self::ACTION_INDEX)
            ->with(self::SUCCESS_KEY, $composer->translator->get(self::SETTING_UPDATED_KEY));
    }
}

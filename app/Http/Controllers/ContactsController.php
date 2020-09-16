<?php

namespace App\Http\Controllers;

use App\Mail\ContactsMail;
use App\Repositories\MenusRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class ContactsController extends SiteController
{
    public function __construct()
    {
        parent::__construct(new MenusRepository(new \App\Menu));

        $this->bar = 'left';
        $this->template = config('settings.theme') . '.contacts';
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {

            $messages = [
                'required' => 'Поле :attribute Обязательно к заполнению',
                'email'    => 'Поле :attribute должно содержать правильный email адрес',
            ];

            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email',
                'text' => 'required'
            ]/*,$messages*/);

            $data = $request->all();

            $result = Mail::to(config('mail.mailers.smtp.email'))->send(new ContactsMail($data));

//            if($result) {
                return redirect()->route('contacts')->with('status', 'Email is send');
//            }
        }

        $this->title = 'Контакты';
        $this->keywords = 'Контакты keywords';
        $this->meta_desc = 'Контакты meta_desc';

        $content = view(config('settings.theme') . '.contact_content')->render();
        $this->vars = Arr::add($this->vars, 'content', $content);

        $this->contentLeftBar = view(config('settings.theme') . '.contact_bar')->render();

        return $this->renderOutput();
    }
}

<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Task;

use App\Models\Task;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Access\Impersonation;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class TaskAddChildScreen extends Screen
{
    /**
     * @var Task
     */
    public $parent;
    public $task;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @param Task $parent
     *
     * @return array
     */
    public function query(Task $parent): iterable
    {

        return [
            'parent'       => $parent,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Add child';
    }

    /**
     * Display header description.
     *
     * @return string|null
     */
    public function description(): ?string
    {
        return '';
    }


    /**
     * The screen's action buttons.
     *
     * @return Action[]
     */
    public function commandBar(): iterable
    {
        return [

            Button::make(__('Save'))
                ->icon('check')
                ->method('addChild'),
        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [

            Layout::rows([
                Input::make('task.name')
                    ->title('Name')
                    ->required()
                    ->placeholder('Enter task name')
                    ->help('The name of the task to be created.'),

                Relation::make('task.user_id')
                    ->fromModel(User::class, 'name')
                    ->searchColumns('email')
                    ->required()
                    ->title('Select for Eloquent model'),
            ]),


        ];
    }



    public function addChild(Request $request, Task $parent)
    {
        // Validate form data, save task to database, etc.

        $request->validate([
            'task.name' => 'required|max:255',
            'task.user_id'  => 'required'
        ]);

        $task = new Task();
        $task->name = $request->input('task.name');
        $task->user_id = $request->input('task.user_id');
        $task->parent_id = $parent->id;
        $task->save();

        Toast::info(__('Task was saved.'));

        return redirect()->route('platform.tasks');
    }




}

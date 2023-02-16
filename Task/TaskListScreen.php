<?php

namespace App\Orchid\Screens\Task;

use App\Models\Task;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;

class TaskListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'tasks' => Task::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Task List';
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return 'Orchid Quickstart';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Task')
                ->modal('taskModal')
                ->method('create')
                ->icon('plus'),

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('tasks', [
                TD::make('id'),
                TD::make('name'),
                TD::make('user_id'),
                TD::make('parent_id'),

                TD::make('Actions')
                    ->alignRight()
                    ->render(fn (Task $task) => DropDown::make()
                        ->icon('options-vertical')
                        ->list([

                            Button::make('Delete Task')
                                ->confirm('After deleting, the task will be gone forever.')
                                ->method('delete', ['task' => $task->id]),
                            Link::make(__('Add child'))
                                ->route('platform.task.addchild', $task->id)
                                ->icon('pencil'),
                        ])),
            ]),
            Layout::modal('taskModal', Layout::rows([
                Input::make('task.name')
                    ->title('Name')
                    ->placeholder('Enter task name')
                    ->help('The name of the task to be created.'),

                Relation::make('task.user_id')
                    ->fromModel(User::class, 'name')
                    ->searchColumns('email')
                    ->title('Select for Eloquent model'),
            ]))
                ->title('Create Task')
                ->applyButton('Add Task'),
        ];
    }

    public function create(Request $request)
    {
        // Validate form data, save task to database, etc.

        $request->validate([
            'task.name' => 'required|max:255',
            'task.user_id'  => 'required'
        ]);

        $task = new Task();
        $task->name = $request->input('task.name');
        $task->user_id = $request->input('task.user_id');
        $task->save();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function delete(Task $task)
    {
        $task->delete();
    }
}

@component('mail::message')
# {{ __('reminders.subject') }}

{{ __('reminders.greeting', ['name' => $officer->name]) }}

{{ __('reminders.intro') }}

@if (count($milestones) > 0)
## {{ __('reminders.milestones_heading') }}

@foreach ($milestones as $milestone)
- **{{ $milestone['employee_name'] }}** ({{ $milestone['case_type_label'] }}) — {{ $milestone['milestone_label'] }} [{{ __('reminders.status_'.$milestone['status']) }}], {{ __('reminders.due_date', ['date' => $milestone['due_date']]) }} — [{{ __('reminders.view_case') }}]({{ route('cases.show', $milestone['case_id']) }})
@endforeach
@endif

@if (count($tasks) > 0)
## {{ __('reminders.tasks_heading') }}

@foreach ($tasks as $task)
- **{{ $task['employee_name'] }}** ({{ $task['case_type_label'] }}) — {{ $task['title'] }}, {{ __('reminders.due_date', ['date' => $task['due_date']]) }} — [{{ __('reminders.view_case') }}]({{ route('cases.show', $task['case_id']) }})
@endforeach
@endif

{{ __('reminders.footer') }}
@endcomponent

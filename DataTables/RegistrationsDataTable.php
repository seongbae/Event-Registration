<?php

namespace App\Modules\Event\DataTables;

use App\Modules\Event\Models\Registration;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use App\Helpers\Helper;

class RegistrationsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            //->orderBy('id')
            // ->orderColumn('id', function ($query, $order) {
            //          $query->orderBy('id', $order);
            //      });
            ->addColumn('formatted_date', function($row) {
                return Helper::formatDate($row->created_at);
            })
            ->addColumn('action', function($row) {
                return '<a href="/admin/events/'. $row->id .'/edit" class="btn btn-primary">Edit</a><form action="'.route('events.destroy', $row->id).'" method="POST" class="d-inline">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="submit" name="submit" value="Remove" class="btn btn-danger " onClick="return confirm(\'Are you sure?\')">
                            '.csrf_field().'
                          </form>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Event $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Registration $model)
    {
        return $model->with('event')->has('event')->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('events-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(0,'desc')
                    ->buttons(
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('created_at')->title('Registered on'),
            Column::make('first_name'),
            Column::make('last_name'),
            Column::make('email'),
            Column::make('phone'),
            Column::make('payment_status')->title('Status'),
            Column::make('event.name')->title('Event'),
            // Column::computed('action')
            //       ->exportable(false)
            //       ->printable(false)
            //       ->width(200)
            //       ->addClass('text-center'),
            
            
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Events_' . date('YmdHis');
    }
}

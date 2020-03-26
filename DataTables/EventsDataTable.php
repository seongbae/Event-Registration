<?php

namespace App\Modules\Event\DataTables;

use App\Modules\Event\Models\Event;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class EventsDataTable extends DataTable
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
            ->addColumn('action', function($row) {
                return '<a href="/events/'. $row->id .'" class="btn btn-primary" target="_blank">View</a> <a href="/admin/events/'. $row->id .'/edit" class="btn btn-primary">Edit</a><form action="'.route('events.destroy', $row->id).'" method="POST" class="d-inline">
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
    public function query(Event $model)
    {
        return $model->orderBy('created_at','desc')->newQuery();
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
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
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
            Column::make('name'),
            Column::make('datetime')->title('Date & Time'),
            Column::make('address')->title('Location'),
            Column::make('price')->title('Price'),
            Column::computed('action')->title('')
                  ->exportable(false)
                  ->printable(false)
                  ->addClass('text-right'),
            
            
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

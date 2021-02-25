<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimelineReport
{
    /**
     * @param Carbon $date
     * @param null $excel
     * @return array|string
     */
    public function day (Carbon $date, $excel = null)
    {
        $report = $this->whereDate('created_at', $date->format('Y-m-d'))->get();
        if (is_null($excel))
            return $this->buildReportArray($report);

        return $this->buildExcel( 'botanik-'.$excel.'-day-report-'.$date->format('Y-m-d'), $excel, $this->buildReportArray($report) );
    }

    /**
     * @param null $excel
     * @return array|string
     */
    public function today ($excel = null)
    {
        $report = $this->whereDate('created_at', Carbon::today()->format('Y-m-d'))->get();
        if (is_null($excel))
            return $this->buildReportArray($report);

        return $this->buildExcel( 'botanik-'.$excel.'-today-report-'.Carbon::today()->format('Y-m-d'), $excel, $this->buildReportArray($report) );
    }

    /**
     * @param null $excel
     * @return array|string
     */
    public function week ($excel = null)
    {
        $reports = $this->whereDate('created_at', '>=', Carbon::now()->startOfWeek()->format('Y-m-d'))->get();
        if (is_null($excel))
            return $this->buildReportArray($reports);

        return $this->buildExcel( 'botanik-'.$excel.'-week-report-'.Carbon::now()->startOfWeek()->format('Y-m-d').'-'.Carbon::now()->format('Y-m-d'), $excel, $this->buildReportArray($reports) );
    }

    /**
     * @param null $excel
     * @return array|string
     */
    public function month ($excel = null)
    {
        $reports = $this->whereDate('created_at', '>=', Carbon::now()->startOfMonth()->format('Y-m-d'))->get();
        if (is_null($excel))
            return $this->buildReportArray($reports);

        return $this->buildExcel( 'botanik-'.$excel.'-month-report-'.Carbon::now()->startOfMonth()->format('Y-m-d').'-'.Carbon::now()->format('Y-m-d'), $excel, $this->buildReportArray($reports) );
    }

    /**
     * @param null $excel
     * @return array|string
     */
    public function year ($excel = null)
    {
        $reports = $this->whereDate('created_at', '>=', Carbon::now()->startOfYear()->format('Y-m-d'))->get();
        if (is_null($excel))
            return $this->buildReportArray($reports);

        return $this->buildExcel( 'botanik-'.$excel.'-year-report-'.Carbon::now()->startOfYear()->format('Y-m-d').'-'.Carbon::now()->format('Y-m-d'), $excel, $this->buildReportArray($reports) );
    }

    /**
     * @param null $excel
     * @return array|string
     */
    public function allTime ($excel = null)
    {
        $reports = $this->all();
        if (is_null($excel))
            return $this->buildReportArray($reports);

        return $this->buildExcel( 'botanik-'.$excel.'-all-time-report-'.Carbon::now()->format('Y-m-d'), $excel, $this->buildReportArray($reports) );
    }

    /**
     * @param string $start
     * @param string $end
     * @param null $excel
     * @return array|string
     */
    public function customDate (string $start, string $end = '', $excel = null)
    {
        try {
            $start = Carbon::parse($start)->format('Y-m-d');
        } catch (\Exception $e) {
            return [];
        }

        $name = 'botanik-'.$excel.'-report-from-'.$start;

        if (strlen($end)) {
            try {
                $end = Carbon::parse($end)->format('Y-m-d');
            } catch (\Exception $e) {
                return [];
            }
            $name .= '-to-'.$end;
            $reports = $this->whereBetween('created_at', [$start, $end])->get();
        }
        else
            $reports = $this->whereDate('created_at', '>=', Carbon::parse($start)->format('Y-m-d'))->get();

        if (is_null($excel))
            return $this->buildReportArray($reports);

        return $this->buildExcel($name, $excel, $this->buildReportArray($reports) );
    }
}
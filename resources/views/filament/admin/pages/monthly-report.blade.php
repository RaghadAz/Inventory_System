<div>
    <x-filament-panels::page>

        <div
            style="background: #1e1e24; padding: 25px; border-radius: 15px; border: 1px solid #2d2d35; margin-bottom: 20px;">
            <div
                style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; color: #e2e8f0; font-weight: bold;">
                <span style="font-size: 20px;">📅</span>
                <h3 style="font-size: 16px;"> Customize Detailed Financial Reporting Period</h3>
            </div>
            <form wire:submit.prevent="getReportData">
                {{ $this->form }}
            </form>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
            <button wire:click="exportToExcel" wire:loading.attr="disabled"
                style="background: #10b981; color: white; padding: 10px 20px; border-radius: 8px; font-weight: bold; font-size: 14px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.3s;"
                onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                <svg wire:loading.remove style="width: 18px; height: 18px;" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span wire:loading.remove>Export Financial Activity Report to Excel </span>
                <span wire:loading> Preparing...</span>
            </button>
        </div>

        <div style="background: #111111; border-radius: 15px; border: 1px solid #2d2d35; overflow: hidden;">
            <div
                style="padding: 20px; border-bottom: 1px solid #2d2d35; color: #94a3b8; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                <span>📈</span>
                <span> Daily Profit & Loss Breakdown for the Month</span>
            </div>

            <table style="width: 100%; border-collapse: collapse; direction: rtl; text-align: right;">
                <thead>
                    <tr style="background: #1e1e24; color: #ffffff; border-bottom: 2px solid #2d2d35;">
                        <th style="padding: 15px; font-size: 15px; font-weight: bold;">Date</th>
                        <th style="padding: 15px; font-size: 15px; font-weight: bold;">Transaction Count </th>
                        <th style="padding: 15px; font-size: 15px; font-weight: bold;">Total Sales </th>
                        <th style="padding: 15px; font-size: 15px; font-weight: bold;">Total Expenses </th>
                        <th style="padding: 15px; font-size: 15px; font-weight: bold;">Net Profit / Loss</th>
                    </tr>
                </thead>
                <tbody style="color: #e2e8f0;">
                    @forelse($monthlyDetails as $index => $row)
                        <tr
                            style="background: {{ $index % 2 == 0 ? '#111111' : '#18181b' }}; border-bottom: 1px solid #2d2d35;">
                            <td style="padding: 15px; font-size: 14px; color: #94a3b8;">{{ $row->date }}</td>
                            <td style="padding: 15px; font-size: 14px;">{{ $row->count }} Sales Transaction </td>
                            <td style="padding: 15px; font-size: 15px; font-weight: bold;">
                                {{ number_format($row->sales) }} SYP</td>
                            <td style="padding: 15px; font-size: 15px; font-weight: bold; color: #ef4444;">
                                {{ number_format($row->expenses) }} SYP</td>
                            <td style="padding: 15px; font-size: 15px; font-weight: bold;">
                                @if ($row->profit > 0)
                                    <span style="color: #10b981;">+{{ number_format($row->profit) }} SYP (Profit)</span>
                                @elseif($row->profit < 0)
                                    <span style="color: #ef4444;">{{ number_format($row->profit) }} SYP (Loss)</span>
                                @else
                                    <span>0 SYP</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 40px; text-align: center; color: #64748b;">
                                No data available for the selected period </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </x-filament-panels::page>

    @livewire('notifications')
</div>

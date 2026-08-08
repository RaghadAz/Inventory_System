<?php

namespace App\Filament\Admin\Resources\Sales;

use App\Filament\Admin\Resources\Sales\Pages\CreateSale;
use App\Filament\Admin\Resources\Sales\Pages\EditSale;
use App\Filament\Admin\Resources\Sales\Pages\ListSales;
use App\Models\Product;
use App\Models\Sale;
use BackedEnum;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Log;



class SaleResource extends Resource
{

    protected static ?string $model = Sale::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Sales';


    public static function canViewAny(): bool
    {
        return (string) auth()->user()?->getAttribute('role') === 'admin';
    }


    public static function form(Schema $schema): Schema
    {
        return $schema->schema([

            Section::make('📋 Invoice Details')->schema([
                Grid::make(3)->schema([
                    Select::make('payment_type')
                        ->label('💳 Payment Method')
                        ->options([
                            'cash' => '💵 Cash',
                            'debt' => '📝 Dept',
                        ])
                        ->default('cash')
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $total = floatval($get('total_price') ?? 0);

                            if ($state === 'cash') {
                                $set('paid_amount', $total);
                                $set('paid_amount_display', $total);
                                $set('remaining_price', 0);
                                $set('customer_name', null);
                            } else {
                                $paid = floatval($get('paid_amount') ?? 0);
                                $set('paid_amount_display', $paid);
                                $set('remaining_price', $total - $paid);
                            }
                            self::updateTotals($get, $set);
                        }),

                    TextInput::make('customer_name')
                        ->label('👤 Customer Name ')
                        ->required(fn($get) => $get('payment_type') === 'debt')
                        ->hidden(fn($get) => $get('payment_type') === 'cash')
                        ->live(),

                    TextInput::make('paid_amount')
                        ->label('💰 Paid Amount')
                        ->numeric()
                        ->default(0)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, $get, $set) {
                            $total = floatval($get('total_price') ?? 0);
                            $paid = floatval($state);
                            $set('remaining_price', $total - $paid);
                            self::updateTotals($get, $set);
                        })
                        ->suffix('SYP')
                        ->disabled(fn($get) => $get('payment_type') === 'cash')
                        ->dehydrated(),

                ]),
            ]),
            Repeater::make('items')
                ->relationship('saleItems')
                ->label('🛒 Items')
                ->required()
                ->live()
                ->collapsible()
                ->schema([
                    Grid::make(12)->schema([

                        Select::make('product_id')
                            ->label('📦 Product')
                            ->columnSpan(4)
                            ->options(
                                Product::query()
                                    ->where('quantity', '>', 0)
                                    ->get()
                                    ->mapWithKeys(fn($p) => [
                                        $p->id => "{$p->name} (📊 Quantity: {$p->quantity})"
                                    ])
                            )
                            ->searchable()
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $product = Product::find($state);
                                if ($product) {
                                    $set('price', $product->price);
                                    $set('cost_price', $product->cost_price);
                                    $set('available_stock', $product->quantity);
                                    $set('quantity', 1);

                                    $lineTotal = 1 * $product->price;
                                    $set('line_total', round($lineTotal, 2));

                                    self::updateTotals($get, $set);
                                }
                            }),

                        TextInput::make('quantity')
                            ->label('🔢 Quantity')
                            ->columnSpan(2)
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $get, $set) {
                                $available = floatval($get('available_stock') ?? 0);
                                $qty = floatval($state);

                                if ($qty > $available) {
                                    Notification::make()
                                        ->title('⚠️ Stock Limit Exceeded!')
                                        ->body("Requested quantity ({$qty}) is greater than available ({$available})")
                                        ->danger()
                                        ->send();

                                    $set('quantity', $available);
                                    $qty = $available;
                                }

                                $price = floatval($get('price') ?? 0);
                                $lineTotal = $qty * $price;
                                $set('line_total', round($lineTotal, 2));

                                self::updateTotals($get, $set);
                            }),

                        TextInput::make('price')
                            ->label(' 💵 Sale Price')
                            ->columnSpan(3)
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($get, $set) {
                                $qty = floatval($get('quantity') ?? 1);
                                $price = floatval($get('price') ?? 0);
                                $lineTotal = $qty * $price;
                                $set('line_total', round($lineTotal, 2));

                                self::updateTotals($get, $set);
                            })
                            ->suffix('SYP'),

                        TextInput::make('line_total')
                            ->label('🧮 Line Total')
                            ->columnSpan(3)
                            ->numeric()
                            ->readOnly()
                            ->suffix('SYP')
                            ->live(),

                        Hidden::make('cost_price')->default(0),
                        Hidden::make('available_stock')->default(0),
                        Hidden::make('line_profit')->default(0),

                    ]),
                ])
                ->afterStateUpdated(function ($get, $set) {
                    self::updateTotals($get, $set);
                }),
            Section::make('📊 Invoice Summary')
                ->icon('heroicon-o-calculator')
                ->schema([
                    Grid::make(3)->schema([

                        TextInput::make('total_price')
                            ->label('💰 Total')
                            ->readOnly()
                            ->numeric()
                            ->suffix('SYP')
                            ->live()
                            ->default(0)
                            ->dehydrated()
                            ->extraInputAttributes([
                                'style' => 'font-size: 18px; font-weight: 700; color: #3b82f6;'
                            ])->afterStateUpdated(function ($get, $set) {
                                $qty = floatval($get('quantity') ?? 1);
                                $price = floatval($get('price') ?? 0);
                                $lineTotal = $qty * $price;
                                $set('line_total', round($lineTotal, 2));

                                self::updateTotals($get, $set);
                            }),

                        TextInput::make('paid_amount_display')
                            ->label('💵 Paid')
                            ->readOnly()
                            ->numeric()
                            ->suffix('SYP')
                            ->live()
                            ->default(0)
                            ->dehydrated(),

                        TextInput::make('remaining_price')
                            ->label('📋 Remaining')
                            ->readOnly()
                            ->numeric()
                            ->suffix('SYP')
                            ->live()
                            ->default(0)
                            ->dehydrated()
                            ->extraInputAttributes([
                                'style' => 'font-size: 18px; font-weight: 700;'
                            ]),

                        TextInput::make('total_profit')
                            ->label('📈 Net Profit')
                            ->columnSpan(3)
                            ->readOnly()
                            ->numeric()
                            ->suffix('SYP')
                            ->live()
                            ->default(0)
                            ->dehydrated()
                            ->extraInputAttributes([
                                'style' => 'font-size: 18px; font-weight: 700; color: #10b981;'
                            ]),

                    ]),
                ]),

        ]);
    }
    protected static function updateTotals($get, $set): void
    {
        $items = $get('items') ?? [];

        if (empty($items) || !is_array($items)) {
            $items = [];
        }

        $totalPrice = 0;
        $totalProfit = 0;

        foreach ($items as $index => $item) {
            if (!is_array($item) || empty($item['product_id'])) {
                continue;
            }

            $qty = floatval($item['quantity'] ?? 0);
            $price = floatval($item['price'] ?? 0);
            $costPrice = floatval($item['cost_price'] ?? 0);

            $lineTotal = $qty * $price;
            $lineProfit = ($price - $costPrice) * $qty;

            $totalPrice += $lineTotal;
            $totalProfit += $lineProfit;
        }

        $paymentType = $get('payment_type');

        if ($paymentType === 'cash') {
            $paid = $totalPrice;
            $remaining = 0;
        } else {
            $paidAmount = floatval($get('paid_amount') ?? 0);
            $paid = $paidAmount;
            $remaining = $totalPrice - $paidAmount;
        }

        $set('total_price', round($totalPrice, 2));
        $set('total_profit', round($totalProfit, 2));
        $set('paid_amount', round($paid, 2));
        $set('paid_amount_display', round($paid, 2));
        $set('remaining_price', round($remaining, 2));

        \Log::info('updateTotals', [
            'total_price' => $totalPrice,
            'total_profit' => $totalProfit,
            'items_count' => count($items),
        ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        return self::processSaleData($data);
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        return self::processSaleData($data);
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['saleItems.product']);
    }
    protected static function processSaleData(array $data): array
    {
        $items = $data['items'] ?? [];

        if (empty($items) || !is_array($items)) {
            $data['total_price'] = 0;
            $data['total_profit'] = 0;
            $data['paid_amount'] = 0;
            $data['paid_amount_display'] = 0;
            $data['remaining_price'] = 0;
            return $data;
        }

        $totalPrice = 0;
        $totalProfit = 0;

        $productIds = collect($items)
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $products = !empty($productIds)
            ? Product::whereIn('id', $productIds)->get()->keyBy('id')
            : collect();

        $processedItems = [];
        foreach ($items as $index => $item) {
            if (!is_array($item) || empty($item['product_id'])) {
                continue;
            }

            $qty = floatval($item['quantity'] ?? 1);
            $price = floatval($item['price'] ?? 0);

            $lineTotal = $qty * $price;
            $totalPrice += $lineTotal;

            $item['price']       = $price;
            $item['quantity']    = $qty;
            $item['cost_price']  = $costPrice;
            $item['line_profit'] = round($lineProfit, 2);
            $item['total']       = round($lineTotal, 2);

            $product = $products[$item['product_id']] ?? null;
            if ($product) {
                if ($qty > $product->quantity) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items.' . $index . '.quantity' => " Not available {$product->name}. Available: {$product->quantity}",
                    ]);
                }

                $costPrice = floatval($product->cost_price ?? 0);
                $lineProfit = ($price - $costPrice) * $qty;
                $totalProfit += $lineProfit;

                $item['cost_price'] = $costPrice;
                $item['line_profit'] = round($lineProfit, 2);

                $costPrice = floatval($product->cost_price ?? 0);
                $lineProfit = ($price - $costPrice) * $qty;
                $totalProfit += $lineProfit;

                $item['cost_price'] = $costPrice;
                $item['line_profit'] = round($lineProfit, 2);
            }

            $processedItems[] = $item;
        }

        $data['items'] = $processedItems;

        $paymentType = $data['payment_type'] ?? 'cash';

        if ($paymentType === 'cash') {
            $data['paid_amount'] = round($totalPrice, 2);
            $data['paid_amount_display'] = round($totalPrice, 2);
            $data['remaining_price'] = 0;
            $data['customer_name'] = null;
        } else {
            $paidAmount = floatval($data['paid_amount'] ?? 0);
            $remaining = $totalPrice - $paidAmount;

            $data['paid_amount'] = round($paidAmount, 2);
            $data['paid_amount_display'] = round($paidAmount, 2);
            $data['remaining_price'] = round($remaining, 2);
        }

        $data['total_price'] = round($totalPrice, 2);
        $data['total_profit'] = round($totalProfit, 2);

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),

                TextColumn::make('customer_name')
                    ->label('👤 Customer')
                    ->formatStateUsing(fn($state) => $state ?? '💵 Cash'),

                TextColumn::make('products_list')
                    ->label('📦 Products')
                    ->state(function ($record) {
                        $grouped = [];

                        foreach ($record->saleItems as $item) {
                            $id = $item->product_id;
                            $name = $item->product?->name ?? ' Deleted Product ';
                            $qty = (int) $item->quantity;

                            if (!isset($grouped[$id])) {
                                $grouped[$id] = ['name' => $name, 'qty' => 0];
                            }
                            $grouped[$id]['qty'] += $qty;
                        }

                        $lines = [];
                        foreach ($grouped as $product) {
                            $lines[] = "• {$product['name']} ({$product['qty']})";
                        }

                        return implode("<br>", $lines);
                    })
                    ->html()
                    ->wrap(),

                TextColumn::make('total_price')->label('💰 Total')->money('SYP')->sortable(),
                TextColumn::make('paid_amount')->label('💵 Paid')->money('SYP'),
                TextColumn::make('remaining_price')->label('📋 Remaining')->money('SYP')->color('danger'),
                TextColumn::make('total_profit')->label('📈 Profit')->money('SYP')->color('success'),
                TextColumn::make('payment_type')->label('💳 Payment')->badge(),
                TextColumn::make('created_at')->label('📅 Date')->dateTime('Y-m-d H:i'),
                ViewColumn::make('delete')->label('Actions')->view('invoices.delete-button'),

                ViewColumn::make('print')
                    ->label('🖨️ Print')
                    ->view('invoices.print-button')
                    ->alignCenter(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'create' => CreateSale::route('/create'),
            'edit' => EditSale::route('/{record}/edit'),
        ];
    }
}

@php
    header('Content-Type: application/json');
    $categoriesData = $categories->map(function ($category) {
        return [
            'id' => $category->id,
            'name' => $category->name ?? 'No Name',
        ];
    })->toArray();
    echo json_encode($categoriesData);
@endphp

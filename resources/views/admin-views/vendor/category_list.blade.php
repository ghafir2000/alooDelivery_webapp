
@php
    header('Content-Type: application/json');
    echo json_encode($categories->map(function ($category) {
        return [
            'id' => $category->id,
            'name' => $category->name,
        ];
    })->toArray());
@endphp


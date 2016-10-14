# A fractal serializer that works fine with vuetable.

This package gives you a fractal serializer that makes it easier to work with [vuetable](https://github.com/ratiw/vuetable-2/) pagination.

## About the VueTableSerializer

If you look into the source of [vuetable pagination](https://github.com/ratiw/vuetable-2/blob/master/src/components/VuetablePaginationMixin.vue) you can see what it expects to work.

```
{
    last_page: 10,
    current_page: 1,
    total: 100,
    from: 1,
    to: 10
}
```

This is pretty annoying because by default when you return a fractal paginated collection, by default it does not return the same keys for the pagination. It's more something like this:

```
{
    total: 10,
    count: 100,
    per_page: 10,
    current_page: 2,
    total_pages: 10,
    links: {
        next: 'my-app.dev/books?page=2',
        pref: 'my-app.dev/books?page=1'
    }
}
```

So I came up with this serializer that makes it easier to work with vuetables' default settings. I'll show you how.

## Installation

You can install the package via composer:

```
composer require depsimon/fractal-vuetable-serializer
```

## Usage

Use the VueTableSerializer in your backend code.

```
use DepSimon\FractalVueTableSerializer\VueTableSerializer;
$manager->setSerializer(new VueTableSerializer());
```

If you're using Laravel, I suggest you to use the [laravel-fractal](https://github.com/spatie/laravel-fractal) package that makes it really easy to use fractal.
In this case you can use it like this :

```
$paginator = Book::paginate(5);
$books = $paginator->getCollection();

fractal()
    ->collection($books, new TestTransformer())
    ->serializeWith(new \DepSimon\FractalVueTableSerializer\VueTableSerializer())
    ->paginateWith(new IlluminatePaginatorAdapter($paginator))
    ->toArray();
```

Here's an example VueJS component taking advantage of this serializer.
```
<template>
    <div>
        <vuetable
            ref="vuetable"
            api-url="http://my-app.dev/books"
            :fields="fields"
            :pagination-path="paginationPath"
            :pagination-component="paginationComponent"
            @vuetable:pagination-data="onPaginationData"
            @vuetable:load-success="onLoadSuccess"></vuetable>
        <vuetable-pagination-info
            ref="paginationInfo"></vuetable-pagination-info>
        <component
            ref="pagination"
            :is="paginationComponent"
            @vuetable-pagination:change-page="onChangePage"></component>
    </div>
</template>
<script>
    export default {
        data: function data() {
            return {
                fields: [
                    {
                        name: 'title',
                        title: 'Title'
                    },
                    {
                        name: 'author',
                        title: 'Author'
                    }
                ],
                paginationPath: 'meta.pagination',
                paginationComponent: 'vuetable-pagination'
            }
        },


        methods: {
            onLoadSuccess: function (response) {
                this.$refs.paginationInfo.setPaginationData(response.data);
            },
            onChangePage: function (page) {
                this.$refs.vuetable.changePage(page);
            },
            onPaginationData: function (tablePagination) {
                this.$refs.paginationInfo.setPaginationData(tablePagination);
                this.$refs.pagination.setPaginationData(tablePagination);
            }
        }
    }
</script>
```
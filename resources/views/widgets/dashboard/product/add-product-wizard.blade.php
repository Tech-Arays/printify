<div class="add-product-wizard">
   
    <div class="row">
        <div class="col-xs-12 p-0">
        
            <ul class="breadcrumb">
                <li>
                    <a
                        v-if="categoriesStepIsEnabled"
                        href="#" @click.prevent="clearCategories">
                        @lang('actions.all_categories')
                    </a>
                    <a
                        v-if="!categoriesStepIsEnabled"
                        href="#" @click.prevent="clearFilteredGarment">
                        @lang('actions.all_categories')
                    </a>
                </li>
            </ul>  
            <ul>  
                <li v-for="category in selectedCategories">
                    <a href="#" @click.prevent="clearFilteredGarment">
                        @{{ category.name }}
                    </a>
                </li>
            <ul>    
    </div>
</div>

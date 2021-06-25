import {ModuleWithProviders, NgModule} from '@angular/core';
import { CommonModule } from '@angular/common';

import { ContentRoutingModule } from './content-routing.module';
import { PostsComponent } from './posts/posts.component';
import {ThemeModule} from "../../@theme/theme.module";
import {Ng2SmartTableModule} from "ng2-smart-table";
import {ContentComponent} from "./content.component";
import { CategoryComponent } from './category/category.component';
import { TagComponent } from './tag/tag.component';
import { PostsActionComponent } from './posts-action/posts-action.component';
import {PostModule} from "../../@theme/components/post/post.module";
import {MetaContainerModule} from "../../@theme/components/meta/meta-container.module";


@NgModule({
    declarations: [
      ContentComponent,
      PostsComponent,
      CategoryComponent,
      TagComponent,
      PostsActionComponent,
    ],
    imports: [
      CommonModule,
      ContentRoutingModule,
      Ng2SmartTableModule,
      ThemeModule,
      PostModule,
      MetaContainerModule,
    ]
})
export class ContentModule {
  static forRoot(): ModuleWithProviders<ContentModule> {
    return {
      ngModule: ContentModule,
      providers: [
      ],
    };
  }
}

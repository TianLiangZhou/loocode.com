import {ModuleWithProviders, NgModule} from '@angular/core';
import { CommonModule } from '@angular/common';

import { ContentRoutingModule } from './content-routing.module';
import { PostsComponent } from './posts/posts.component';
import {ThemeModule} from "../../@theme/theme.module";
import {Ng2SmartTableModule} from "ng2-smart-table";
import {ContentComponent} from "./content.component";
import {CKEditorModule} from "@ckeditor/ckeditor5-angular";
import { CategoryComponent } from './category/category.component';
import { TagComponent } from './tag/tag.component';
import { PostsActionComponent } from './posts-action/posts-action.component';
import {MarkdownEditorModule} from "../../@theme/components/markdown-editor/markdown-editor.module";
import {PostModule} from "../../@theme/components/post/post.module";
import {NbCardModule} from "@nebular/theme";


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

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { PageRoutingModule } from './page-routing.module';
import {NewComponent} from "./new/new.component";
import {PageComponent} from "./page.component";
import {ThemeModule} from "../../@theme/theme.module";
import {MarkdownEditorModule} from "../../@theme/components/markdown-editor/markdown-editor.module";
import {CKEditorModule} from "@ckeditor/ckeditor5-angular";
import {Ng2SmartTableModule} from "ng2-smart-table";
import {PostModule} from "../../@theme/components/post/post.module";


@NgModule({
  declarations: [
    NewComponent,
    PageComponent,
  ],
  imports: [
    CommonModule,
    PageRoutingModule,
    ThemeModule,
    MarkdownEditorModule,
    CKEditorModule,
    Ng2SmartTableModule,
    PostModule
  ]
})
export class PageModule { }

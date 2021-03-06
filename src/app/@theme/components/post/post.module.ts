import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {PostComponent} from "./post.component";
import {
  NbListModule
} from "@nebular/theme";
import {MarkdownEditorModule} from "../markdown-editor/markdown-editor.module";
import {CKEditorModule} from "@ckeditor/ckeditor5-angular";
import {CKFinderService} from "../../../@core/services/ckfinder.service";
import {ThemeModule} from "../../theme.module";
import {MetaContainerModule} from "../meta/meta-container.module";



@NgModule({
  declarations: [
    PostComponent,
  ],
  exports: [
    PostComponent,
  ],
  imports: [
    CommonModule,
    ThemeModule,
    MarkdownEditorModule,
    CKEditorModule,
    NbListModule,
    MetaContainerModule,
  ],
  providers: [
    CKFinderService
  ]
})
export class PostModule { }

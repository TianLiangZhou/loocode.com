import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  NbListModule
} from "@nebular/theme";
import {MarkdownEditorModule} from "../markdown-editor/markdown-editor.module";
import {CKEditorModule} from "@ckeditor/ckeditor5-angular";
import {CKFinderService} from "../../../@core/services/ckfinder.service";
import {ThemeModule} from "../../theme.module";
import {MetaComponent} from "./meta.component";



@NgModule({
  declarations: [
    MetaComponent
  ],
  exports: [
    MetaComponent,
  ],
  imports: [
    CommonModule,
    ThemeModule,
    MarkdownEditorModule,
    CKEditorModule,
    NbListModule,
  ],
  providers: [
    CKFinderService
  ]
})
export class MetaContainerModule{ }

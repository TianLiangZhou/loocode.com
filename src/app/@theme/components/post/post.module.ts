import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import {PostComponent} from "./post.component";
import {
  NbAccordionModule,
  NbAutocompleteModule,
  NbButtonModule, NbCardModule,
  NbCheckboxModule, NbDatepickerModule,
  NbFormFieldModule, NbIconModule,
  NbInputModule, NbListModule, NbPopoverModule, NbRadioModule, NbSelectModule,
  NbTagModule
} from "@nebular/theme";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {MarkdownEditorModule} from "../markdown-editor/markdown-editor.module";
import {CKEditorModule} from "@ckeditor/ckeditor5-angular";
import {CKFinderService} from "../../../@core/services/ckfinder.service";
import {ThemeModule} from "../../theme.module";



@NgModule({
  declarations: [PostComponent],
  exports: [
    PostComponent,
  ],
  imports: [
    CommonModule,
    ThemeModule,
    MarkdownEditorModule,
    CKEditorModule,
  ],
  providers: [
    CKFinderService
  ]
})
export class PostModule { }

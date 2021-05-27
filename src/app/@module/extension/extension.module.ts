import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ExtensionRoutingModule } from './extension-routing.module';
import { MetaComponent } from './meta/meta.component';
import { ModelComponent } from './model/model.component';
import {NbButtonModule, NbCardModule, NbCheckboxModule, NbIconModule, NbInputModule, NbSelectModule} from "@nebular/theme";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import {BrowserModule} from "@angular/platform-browser";


@NgModule({
  declarations: [MetaComponent, ModelComponent],
  imports: [
    CommonModule,
    ExtensionRoutingModule,
    NbCardModule,
    NbButtonModule,
    NbSelectModule,
    NbInputModule,
    NbIconModule,
    FormsModule,
    ReactiveFormsModule,
    NbCheckboxModule
  ]
})
export class ExtensionModule { }

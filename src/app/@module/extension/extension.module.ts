import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { ExtensionRoutingModule } from './extension-routing.module';
import { ModelComponent } from './model/model.component';
import { MetaComponent } from './meta/meta.component';
import {NbButtonModule, NbCardModule, NbCheckboxModule, NbIconModule, NbInputModule, NbRadioModule, NbSelectModule} from "@nebular/theme";
import {FormsModule, ReactiveFormsModule} from "@angular/forms";
import { NewModelComponent } from './new-model/new-model.component';
import {BuildMetaComponent} from "../../@theme/components/meta/build-meta.component";
import {Ng2SmartTableModule} from "ng2-smart-table";
import { DynamicModelComponent } from './dynamic-model/dynamic-model.component';
import {MetaContainerModule} from "../../@theme/components/meta/meta-container.module";


@NgModule({
  declarations: [
    MetaComponent,
    ModelComponent,
    NewModelComponent,
    BuildMetaComponent,
    DynamicModelComponent,
  ],
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
    NbCheckboxModule,
    NbRadioModule,
    Ng2SmartTableModule,
    MetaContainerModule
  ],

})
export class ExtensionModule { }
